@extends('layouts.account')
@section('title', 'Meus Pedidos | Dias Sneakers')
@section('content')

<div class="ac-page-title">Meus Pedidos</div>
<div class="ac-page-subtitle">Histórico completo das suas compras.</div>

@forelse($orders as $order)
  <div class="ac-card" style="margin-bottom:12px">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap">
      <div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:1.15rem;font-weight:500;color:#111;margin-bottom:5px">Pedido #{{ $order->id }}</div>
        <div style="font-family:'Montserrat',sans-serif;font-size:0.7rem;color:#888;font-weight:400">{{ $order->created_at->format('d/m/Y \à\s H:i') }}</div>
        <div style="font-family:'Montserrat',sans-serif;font-size:0.75rem;color:#666;margin-top:8px">
          {{ $order->items->count() }} item(s) &bull; {{ $order->total_formatted }}
        </div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
        @if(!($order->payment_status === 'paid' && $order->status === 'pending'))
          @php
            $sc = match($order->status) {
              'delivered' => ['bg'=>'#f0fdf4','color'=>'#16a34a','border'=>'#bbf7d0'],
              'cancelled'  => ['bg'=>'#fff5f5','color'=>'#c00','border'=>'#fecaca'],
              default      => ['bg'=>'#fafaf8','color'=>'#777','border'=>'#e8e4df'],
            };
          @endphp
          <span style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;padding:4px 12px;letter-spacing:0.1em;text-transform:uppercase;
            background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }}">
            {{ $order->status_label }}
          </span>
        @endif

        @if($order->payment_status === 'paid')
          <span style="font-family:'Montserrat',sans-serif;display:inline-flex;align-items:center;gap:5px;font-size:0.62rem;font-weight:600;padding:4px 12px;letter-spacing:0.08em;text-transform:uppercase;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Pagamento confirmado
          </span>
        @elseif($order->payment_status === 'failed')
          <span style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;padding:4px 12px;letter-spacing:0.08em;text-transform:uppercase;background:#fff5f5;color:#c00;border:1px solid #fecaca">
            Pagamento recusado
          </span>
        @elseif($order->payment_status === 'refunded')
          <span style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;padding:4px 12px;letter-spacing:0.08em;text-transform:uppercase;background:#fafaf8;color:#777;border:1px solid #e8e4df">
            Estornado
          </span>
        @else
          <span style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;padding:4px 12px;letter-spacing:0.08em;text-transform:uppercase;background:#fffbeb;color:#92400e;border:1px solid #fde68a">
            Aguardando pagamento
          </span>
        @endif

        <span style="font-family:'Montserrat',sans-serif;font-size:0.68rem;color:#aaa;font-weight:400">via {{ $order->payment_method_label }}</span>

        <a href="{{ route('account.order.show', $order->id) }}" class="ac-btn ac-btn-ghost" style="font-size:0.62rem;padding:8px 18px">
          Ver detalhes
        </a>
      </div>
    </div>
  </div>
@empty
  <div class="ac-card" style="text-align:center;padding:56px">
    <svg width="44" height="44" fill="none" stroke="#ccc" stroke-width="1.2" viewBox="0 0 24 24" style="margin:0 auto 16px;display:block">
      <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
    </svg>
    <p style="font-family:'Montserrat',sans-serif;font-size:0.78rem;color:#aaa;letter-spacing:0.06em;margin-bottom:24px">Você ainda não fez nenhum pedido.</p>
    <a href="{{ route('home') }}" class="ac-btn ac-btn-primary">Explorar produtos</a>
  </div>
@endforelse

<div style="margin-top:8px">{{ $orders->links() }}</div>

@endsection
