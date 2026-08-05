<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $cupons = Coupon::latest()->get();
        return view('admin.cupons.index', compact('cupons'));
    }

    public function create()
    {
        return view('admin.cupons.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:50|unique:coupons,code',
            'type'      => 'required|in:percent,fixed',
            'value'     => 'required|numeric|min:0.01',
            'min_order' => 'nullable|numeric|min:0',
            'max_uses'  => 'nullable|integer|min:1',
            'expires_at'=> 'nullable|date|after:today',
            'active'    => 'boolean',
        ], [
            'code.unique'        => 'Este código já existe.',
            'expires_at.after'   => 'A data de validade deve ser futura.',
        ]);

        $data['code']   = strtoupper($data['code']);
        $data['active'] = $request->boolean('active');

        Coupon::create($data);

        return redirect()->route('admin.cupons.index')->with('success', 'Cupom criado com sucesso!');
    }

    public function edit(Coupon $cupon)
    {
        return view('admin.cupons.edit', compact('cupon'));
    }

    public function update(Request $request, Coupon $cupon)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:50|unique:coupons,code,' . $cupon->id,
            'type'      => 'required|in:percent,fixed',
            'value'     => 'required|numeric|min:0.01',
            'min_order' => 'nullable|numeric|min:0',
            'max_uses'  => 'nullable|integer|min:1',
            'expires_at'=> 'nullable|date',
            'active'    => 'boolean',
        ], [
            'code.unique' => 'Este código já existe.',
        ]);

        $data['code']   = strtoupper($data['code']);
        $data['active'] = $request->boolean('active');

        $cupon->update($data);

        return redirect()->route('admin.cupons.index')->with('success', 'Cupom atualizado!');
    }

    public function destroy(Coupon $cupon)
    {
        $cupon->delete();
        return redirect()->route('admin.cupons.index')->with('success', 'Cupom excluído.');
    }

    public function toggle(Coupon $cupon)
    {
        $cupon->update(['active' => !$cupon->active]);
        return back()->with('success', $cupon->active ? 'Cupom ativado.' : 'Cupom desativado.');
    }
}
