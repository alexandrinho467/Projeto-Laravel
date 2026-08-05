<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\ProductFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images')->withCount('sizes')->orderBy('sort_order')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required', 'name' => 'required', 'price' => 'required|numeric',
        ]);

        $product = Product::create([
            'brand'       => $request->brand,
            'name'        => $request->name,
            'slug'        => Str::slug($request->name) . '-' . time(),
            'badge'       => $request->badge,
            'old_price'   => $request->old_price,
            'price'       => $request->price,
            'ref'         => $request->ref,
            'description' => $request->description,
            'tech'        => $request->tech,
            'active'      => $request->boolean('active', true),
            'sort_order'  => (int) $request->sort_order,
        ]);

        $this->saveRelations($product, $request);

        return redirect()->route('admin.produtos.index')->with('success', 'Produto criado!');
    }

    public function edit(Product $product)
    {
        $product->load(['images','sizes','features']);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'brand' => 'required', 'name' => 'required', 'price' => 'required|numeric',
        ]);

        $product->update([
            'brand'       => $request->brand,
            'name'        => $request->name,
            'badge'       => $request->badge,
            'old_price'   => $request->old_price,
            'price'       => $request->price,
            'ref'         => $request->ref,
            'description' => $request->description,
            'tech'        => $request->tech,
            'active'      => $request->boolean('active'),
            'sort_order'  => (int) $request->sort_order,
        ]);

        // Clear and re-save relations
        $product->images()->delete();
        $product->sizes()->delete();
        $product->features()->delete();
        $this->saveRelations($product, $request);

        return redirect()->route('admin.produtos.index')->with('success', 'Produto atualizado!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.produtos.index')->with('success', 'Produto removido!');
    }

    private function saveRelations(Product $product, Request $request): void
    {
        // Images: expect color_name[], img1[], img2[]
        if ($request->has('color_name')) {
            foreach ($request->color_name as $i => $colorName) {
                $img1 = $request->img1[$i] ?? '';
                $img2 = $request->img2[$i] ?? $img1;

                // Handle upload
                if ($request->hasFile("img1_file.$i")) {
                    $file = $request->file("img1_file.$i");
                    $img1 = 'assets/images/' . $file->getClientOriginalName();
                    $file->move(public_path('assets/images'), $file->getClientOriginalName());
                }
                if ($request->hasFile("img2_file.$i")) {
                    $file = $request->file("img2_file.$i");
                    $img2 = 'assets/images/' . $file->getClientOriginalName();
                    $file->move(public_path('assets/images'), $file->getClientOriginalName());
                }

                if ($img1) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'color_name' => $colorName,
                        'img1'       => $img1,
                        'img2'       => $img2,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        // Sizes: expect sizes[] checked array
        if ($request->has('sizes')) {
            $allSizes   = ['35','36','37','38','39','40','41','42','43','44','45'];
            $activeSizes = $request->sizes ?? [];
            foreach ($allSizes as $sz) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size'       => $sz,
                    'available'  => in_array($sz, $activeSizes),
                ]);
            }
        }

        // Features
        if ($request->has('features')) {
            foreach ($request->features as $fi => $feat) {
                if (trim($feat)) {
                    ProductFeature::create([
                        'product_id' => $product->id,
                        'feature'    => $feat,
                        'sort_order' => $fi,
                    ]);
                }
            }
        }
    }

    public function toggleActive(Product $product)
    {
        $product->update(['active' => !$product->active]);
        return response()->json(['success' => true, 'active' => $product->active]);
    }
}
