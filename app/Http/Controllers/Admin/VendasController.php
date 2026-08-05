<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class VendasController extends Controller
{
    public function index()
    {
        $vendas = OrderItem::select(
                'product_name',
                'product_brand',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(price * qty) as total_revenue')
            )
            ->groupBy('product_name', 'product_brand')
            ->orderByDesc('total_qty')
            ->get();

        $totalReceita = $vendas->sum('total_revenue');
        $totalUnidades = $vendas->sum('total_qty');

        return view('admin.vendas', compact('vendas', 'totalReceita', 'totalUnidades'));
    }
}
