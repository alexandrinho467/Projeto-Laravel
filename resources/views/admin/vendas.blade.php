@extends('layouts.admin')
@section('title', 'Produtos Vendidos | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Produtos Vendidos</h1>
</div>

<div class="stat-grid" style="margin-bottom:32px">
  <div class="stat-card">
    <div class="label">Total de unidades vendidas</div>
    <div class="value">{{ $totalUnidades }}</div>
  </div>
  <div class="stat-card">
    <div class="label">Receita total (produtos)</div>
    <div class="value" style="font-size:1.3rem">AED {{ number_format($totalReceita, 2, '.', ',') }}</div>
  </div>
  <div class="stat-card">
    <div class="label">Produtos distintos vendidos</div>
    <div class="value">{{ $vendas->count() }}</div>
  </div>
</div>

<table class="admin-table">
  <thead>
    <tr>
      <th>Produto</th>
      <th>Marca</th>
      <th>Unidades vendidas</th>
      <th>Receita gerada</th>
    </tr>
  </thead>
  <tbody>
    @forelse($vendas as $v)
      <tr>
        <td style="font-weight:600;font-size:.9rem">{{ $v->product_name }}</td>
        <td style="color:#697386;font-size:.85rem">{{ $v->product_brand }}</td>
        <td>
          <span style="background:#DBEAFE;color:#1D4ED8;padding:3px 12px;border-radius:20px;font-size:.78rem;font-weight:600">
            {{ $v->total_qty }} un.
          </span>
        </td>
        <td style="font-weight:600;color:#059669">AED {{ number_format($v->total_revenue, 2, '.', ',') }}</td>
      </tr>
    @empty
      <tr><td colspan="4" style="text-align:center;color:#666;padding:32px">Nenhum produto vendido ainda.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
