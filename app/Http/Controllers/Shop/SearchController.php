<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));

        $products = collect();

        if ($q !== '') {
            $products = Product::with(['images', 'sizes'])
                ->where('active', true)
                ->where(function ($query) use ($q) {
                    $query->where('name',  'like', "%{$q}%")
                          ->orWhere('brand', 'like', "%{$q}%")
                          ->orWhere('description', 'like', "%{$q}%");
                })
                ->orderBy('sort_order')
                ->get();
        }

        return view('shop.search', compact('products', 'q'));
    }
}
