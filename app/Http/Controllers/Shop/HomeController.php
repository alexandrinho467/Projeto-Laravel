<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with(['images','sizes'])
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        $settings = [
            'banner_img'      => SiteSetting::get('banner_img'),
            'banner_title'    => SiteSetting::get('banner_title', 'Nova Coleção 2025'),
            'banner_subtitle' => SiteSetting::get('banner_subtitle', 'Até 50% Off'),
        ];

        return view('shop.home', compact('products', 'settings'));
    }
}
