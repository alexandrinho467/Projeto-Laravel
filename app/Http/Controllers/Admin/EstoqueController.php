<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index()
    {
        $products = Product::with(['images', 'sizes'])->orderBy('sort_order')->get();
        return view('admin.estoque', compact('products'));
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'sizes' => fn($q) => $q->orderBy('size')]);
        return view('admin.estoque-edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sizes'               => 'required|array',
            'sizes.*.id'          => 'required|exists:product_sizes,id',
            'sizes.*.stock'       => 'required|integer|min:0',
            'sizes.*.stock_alert' => 'required|integer|min:0',
        ]);

        $product->load('images');

        foreach ($request->sizes as $row) {
            $ps       = ProductSize::find($row['id']);
            $oldStock = $ps->stock;
            $newStock = (int) $row['stock'];
            $diff     = $newStock - $oldStock;

            $ps->update([
                'stock'       => $newStock,
                'available'   => $newStock > 0,
                'stock_alert' => (int) $row['stock_alert'],
            ]);

            if ($diff !== 0) {
                StockMovement::create([
                    'product_id'      => $product->id,
                    'product_size_id' => $ps->id,
                    'product_name'    => $product->name,
                    'product_brand'   => $product->brand,
                    'size'            => $ps->size,
                    'type'            => $diff > 0 ? 'entrada' : 'saida',
                    'qty'             => abs($diff),
                    'notes'           => 'Ajuste manual',
                ]);
            }
        }

        return back()->with('success', 'Estoque atualizado!');
    }

    public function addSize(Request $request, Product $product)
    {
        $request->validate([
            'size'        => 'required|string|max:10',
            'stock'       => 'required|integer|min:0',
            'stock_alert' => 'nullable|integer|min:0',
        ], [
            'size.required'  => 'Informe o tamanho.',
            'stock.required' => 'Informe a quantidade.',
        ]);

        $exists = ProductSize::where('product_id', $product->id)
            ->where('size', $request->size)
            ->exists();

        if ($exists) {
            return back()->withErrors(['size' => 'Este tamanho já existe para este produto.']);
        }

        $stock = (int) $request->stock;
        $ps    = ProductSize::create([
            'product_id'  => $product->id,
            'size'        => $request->size,
            'stock'       => $stock,
            'available'   => $stock > 0,
            'stock_alert' => (int) ($request->stock_alert ?? 1),
        ]);

        if ($stock > 0) {
            StockMovement::create([
                'product_id'      => $product->id,
                'product_size_id' => $ps->id,
                'product_name'    => $product->name,
                'product_brand'   => $product->brand,
                'size'            => $request->size,
                'type'            => 'entrada',
                'qty'             => $stock,
                'notes'           => 'Novo tamanho adicionado',
            ]);
        }

        return back()->with('success', "Tamanho {$request->size} adicionado!");
    }

    public function removeSize(ProductSize $size)
    {
        $productId = $size->product_id;
        $size->delete();
        return redirect()->route('admin.estoque.edit', $productId)->with('success', 'Tamanho removido.');
    }

    public function relatorio(Request $request)
    {
        $movements = StockMovement::with('order')
            ->when($request->produto, fn($q) => $q->where('product_name', 'like', "%{$request->produto}%"))
            ->when($request->tipo, fn($q) => $q->where('type', $request->tipo))
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.estoque-relatorio', compact('movements'));
    }
}
