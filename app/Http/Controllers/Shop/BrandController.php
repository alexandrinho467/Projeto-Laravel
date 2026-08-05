<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class BrandController extends Controller
{
    private array $brands = [
        'nike'     => ['label' => 'Nike',     'logo' => 'assets/images/brand-nike.png'],
        'jordan'   => ['label' => 'Jordan',   'logo' => 'assets/images/brand-jordan.png'],
        'balmain'  => ['label' => 'Balmain',  'logo' => 'assets/images/brand-balmain.png'],
        'converse' => ['label' => 'Converse', 'logo' => 'assets/images/brand-converse.png'],
        'mizuno'   => ['label' => 'Mizuno',   'logo' => 'assets/images/brand-mizuno.png'],
        'salomon'     => ['label' => 'Salomon',     'logo' => null],
        'new-balance' => ['label' => 'New Balance', 'logo' => null],
    ];

    public function index()
    {
        return view('shop.brands', ['brands' => $this->brands]);
    }

    public function show(string $slug)
    {
        $info = $this->brands[$slug] ?? ['label' => ucfirst($slug), 'logo' => null];

        $products = Product::with(['images', 'sizes'])
            ->where('active', true)
            ->whereRaw('LOWER(brand) = ?', [strtolower($info['label'])])
            ->orderBy('sort_order')
            ->get();

        return view('shop.category', [
            'products' => $products,
            'titulo'   => $info['label'],
        ]);
    }
}
