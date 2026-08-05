<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class CategoryController extends Controller
{
    public function masculino()
    {
        $products = Product::with(['images', 'sizes'])
            ->where('active', true)
            ->where('gender', 'masculino')
            ->orderBy('sort_order')
            ->get();

        return view('shop.category', [
            'products' => $products,
            'titulo'   => 'Masculino',
        ]);
    }

    public function feminino()
    {
        $products = Product::with(['images', 'sizes'])
            ->where('active', true)
            ->where('gender', 'feminino')
            ->orderBy('sort_order')
            ->get();

        return view('shop.category', [
            'products' => $products,
            'titulo'   => 'Feminino',
        ]);
    }
}
