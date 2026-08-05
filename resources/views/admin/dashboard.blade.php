@extends('layouts.admin')
@section('title', 'Dashboard | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Dashboard</h1>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="label">Pedidos</div>
    <div class="value">{{ $totalOrders }}</div>
  </div>
  <div class="stat-card">
    <div class="label">Receita (pagos)</div>
    <div class="value" style="font-size:1.3rem">AED {{ number_format($totalRevenue,2,'.',',') }}</div>
  </div>
  <div class="stat-card">
    <div class="label">Produtos</div>
    <div class="value">{{ $totalProducts }}</div>
  </div>
  <div class="stat-card">
    <div class="label">Clientes</div>
    <div class="value">{{ $totalCustomers }}</div>
  </div>
</div>

@if($lowStock->isNotEmpty())
<div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:8px;padding:20px 24px;margin-bottom:24px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
    <div style="display:flex;align-items:center;gap:10px">
      <span style="font-size:1.1rem">⚠️</span>
      <span style="font-weight:600;color:#92400E;font-size:.92rem">Alerta de estoque baixo — {{ $lowStock->count() }} tamanho(s)</span>
    </div>
    <a href="{{ route('admin.estoque') }}" style="font-size:.8rem;color:#635BFF;text-decoration:none;font-weight:500">Gerenciar estoque →</a>
  </div>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Produto</th>
        <th>Tamanho</th>
        <th>Estoque atual</th>
        <th>Alerta</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($lowStock as $ps)
      @php $img = $ps->product->images->first(); @endphp
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            @if($img)
              <img src="{{ asset($img->img1) }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px">
            @endif
            <div>
              <div style="font-weight:600;font-size:.88rem;color:#fff">{{ $ps->product->name }}</div>
              <div style="font-size:.75rem;color:#666">{{ $ps->product->brand }}</div>
            </div>
          </div>
        </td>
        <td><span style="background:#EEF2FF;color:#3730A3;padding:3px 10px;border-radius:6px;font-size:.82rem;font-weight:600">{{ $ps->size }}</span></td>
        <td>
          @if($ps->stock === 0)
            <span class="badge badge-red">Esgotado</span>
          @else
            <span class="badge badge-yellow">{{ $ps->stock }} par(es)</span>
          @endif
        </td>
        <td style="color:#666;font-size:.85rem">≤ {{ $ps->stock_alert }}</td>
        <td>
          <a href="{{ route('admin.estoque.edit', $ps->product_id) }}" style="color:#f97316;font-size:.82rem;text-decoration:none">Repor →</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

<h2 style="font-size:1rem;font-weight:600;margin:32px 0 16px">Pedidos dos clientes</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>#</th><th>Cliente</th><th>Produto(s)</th><th>Valor</th><th>Forma de pagamento</th><th>Data</th><th></th>
    </tr>
  </thead>
  <tbody>
    @forelse($customerOrders as $order)
      <tr>
        <td style="color:#666;font-size:.82rem">{{ $order->id }}</td>
        <td>{{ $order->guest_name }}</td>
        <td>
          @foreach($order->items as $item)
            <div style="font-size:.82rem;line-height:1.5">
              {{ $item->product_name }}
              @if($item->qty > 1)<span style="color:#697386"> ×{{ $item->qty }}</span>@endif
            </div>
          @endforeach
        </td>
        <td style="white-space:nowrap">{{ $order->total_formatted }}</td>
        <td style="font-size:.85rem">{{ $order->payment_method_label }}</td>
        <td style="color:#666;font-size:.82rem;white-space:nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        <td><a href="{{ route('admin.orders.show', $order) }}" style="color:#635BFF;font-size:.82rem;font-weight:500">Ver</a></td>
      </tr>
    @empty
      <tr><td colspan="7" style="color:#666;text-align:center;padding:24px">Nenhum pedido de clientes ainda</td></tr>
    @endforelse
  </tbody>
</table>

<h2 style="font-size:1rem;font-weight:600;margin:40px 0 16px">Meus pedidos</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>#</th><th>Produto(s)</th><th>Valor</th><th>Forma de pagamento</th><th>Data</th><th></th>
    </tr>
  </thead>
  <tbody>
    @forelse($adminOrders as $order)
      <tr>
        <td style="color:#666;font-size:.82rem">{{ $order->id }}</td>
        <td>
          @foreach($order->items as $item)
            <div style="font-size:.82rem;line-height:1.5">
              {{ $item->product_name }}
              @if($item->qty > 1)<span style="color:#697386"> ×{{ $item->qty }}</span>@endif
            </div>
          @endforeach
        </td>
        <td style="white-space:nowrap">{{ $order->total_formatted }}</td>
        <td style="font-size:.85rem">{{ $order->payment_method_label }}</td>
        <td style="color:#666;font-size:.82rem;white-space:nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        <td><a href="{{ route('admin.orders.show', $order) }}" style="color:#635BFF;font-size:.82rem;font-weight:500">Ver</a></td>
      </tr>
    @empty
      <tr><td colspan="6" style="color:#666;text-align:center;padding:24px">Nenhum pedido seu ainda</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
