@extends('layouts.admin')
@section('title', 'Pedidos | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Pedidos</h1>
  <div style="display:flex;gap:8px">
    @foreach(['','pending','processing','shipped','delivered','cancelled'] as $s)
      <a href="{{ route('admin.orders.index', $s ? ['status'=>$s] : []) }}"
         style="padding:7px 14px;border-radius:6px;font-size:.8rem;text-decoration:none;font-weight:500;border:1px solid {{ request('status')===$s?'#635BFF':'#E3E8EE' }};background:{{ request('status')===$s?'#635BFF':'#fff' }};color:{{ request('status')===$s?'#fff':'#374151' }}">
        {{ $s ? ucfirst($s) : 'Todos' }}
      </a>
    @endforeach
  </div>
</div>

<table class="admin-table">
  <thead>
    <tr><th>#</th><th>Cliente</th><th>E-mail</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th><th></th></tr>
  </thead>
  <tbody>
    @forelse($orders as $order)
      <tr>
        <td>{{ $order->id }}</td>
        <td>{{ $order->guest_name }}</td>
        <td style="font-size:.82rem;color:#697386">{{ $order->guest_email }}</td>
        <td style="font-weight:600">{{ $order->total_formatted }}</td>
        <td>
          <span class="badge {{ $order->payment_status==='paid'?'badge-green':($order->payment_status==='failed'?'badge-red':'badge-yellow') }}">
            {{ $order->payment_status_label }}
          </span>
        </td>
        <td>
          <span class="badge {{ $order->status==='delivered'?'badge-green':($order->status==='cancelled'?'badge-red':'badge-yellow') }}">
            {{ $order->status_label }}
          </span>
        </td>
        <td style="color:#697386;font-size:.82rem">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        <td><a href="{{ route('admin.orders.show', $order) }}" style="color:#635BFF;font-size:.82rem;font-weight:500">Ver</a></td>
      </tr>
    @empty
      <tr><td colspan="8" style="text-align:center;color:#666;padding:32px">Nenhum pedido</td></tr>
    @endforelse
  </tbody>
</table>
<div style="margin-top:20px">{{ $orders->links() }}</div>
@endsection
