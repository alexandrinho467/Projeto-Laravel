<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(int $id)
    {
        $product = Product::with(['images','sizes','features'])->findOrFail($id);
        $related = Product::with(['images','sizes'])
            ->where('active', true)
            ->where('id', '!=', $id)
            ->limit(4)
            ->get();
        $reviews = Review::where('product_id', $id)
            ->where('approved', true)
            ->latest()
            ->get();
        $avgRating = $reviews->avg('rating');

        return view('shop.product', compact('product', 'related', 'reviews', 'avgRating'));
    }
}
