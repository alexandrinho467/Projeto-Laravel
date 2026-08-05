<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders    = Order::count();
        $totalRevenue   = Order::where('payment_status','paid')->sum('total');
        $totalProducts  = Product::count();
        $totalCustomers = User::where('role','customer')->count();
        $allOrders = Order::with(['items', 'user'])->latest()->limit(100)->get();

        $customerOrders = $allOrders->filter(fn($o) => !$o->user || $o->user->role !== 'admin');
        $adminOrders    = $allOrders->filter(fn($o) => $o->user && $o->user->role === 'admin');

        $lowStock = ProductSize::with('product.images')
            ->whereColumn('stock', '<=', 'stock_alert')
            ->orderBy('stock')
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders','totalRevenue','totalProducts','totalCustomers',
            'customerOrders','adminOrders','lowStock'
        ));
    }
}
