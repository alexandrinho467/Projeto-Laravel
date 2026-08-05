@extends('layouts.account')
@section('title', 'Pedido #' . $order->id . ' | Dias Sneakers')
@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:28px">
  <a href="{{ route('account.orders') }}" class="ac-btn ac-btn-ghost" style="font-size:0.62rem;padding:8px 14px">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Pedidos
  </a>
  <span style="font-family:'Montserrat',sans-serif;font-size:0.68rem;color:#bbb">/</span>
  <span style="font-family:'Montserrat',sans-serif;font-size:0.68rem;color:#888;font-weight:400">Pedido #{{ $order->id }}</span>
</div>

<div class="ac-page-title">Pedido #{{ $order->id }}</div>
<div class="ac-page-subtitle">Realizado em {{ $order->created_at->format('d/m/Y \à\s H:i') }}</div>

@if($order->payment_status === 'paid')
  <div style="display:flex;align-items:center;gap:12px;background:#f0fdf4;border:1px solid #bbf7d0;padding:16px 20px;margin-bottom:20px">
    <div style="width:32px;height:32px;background:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div>
      <div style="font-family:'Montserrat',sans-serif;font-size:0.78rem;font-weight:600;color:#15803d">Pagamento confirmado</div>
      <div style="font-family:'Montserrat',sans-serif;font-size:0.72rem;color:#16a34a;margin-top:2px">O Stripe confirmou o recebimento do seu pagamento.</div>
    </div>
  </div>
@endif

<div class="ac-card" style="margin-bottom:16px">
  <div class="ac-card-title">Status do Pedido</div>
  <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:24px">

    @if(!($order->payment_status === 'paid' && $order->status === 'pending'))
    <div>
      <div style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;color:#aaa;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:8px">Pedido</div>
      @php
        $sc = match($order->status) {
          'delivered' => ['bg'=>'#f0fdf4','color'=>'#16a34a','border'=>'#bbf7d0'],
          'cancelled'  => ['bg'=>'#fff5f5','color'=>'#c00','border'=>'#fecaca'],
          default      => ['bg'=>'#fafaf8','color'=>'#777','border'=>'#e8e4df'],
        };
      @endphp
      <span style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;padding:5px 14px;letter-spacing:0.1em;text-transform:uppercase;
        background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }}">
        {{ $order->status_label }}
      </span>
    </div>
    @endif

    <div>
      <div style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;color:#aaa;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:8px">Pagamento</div>
      @php
        $pc = match($order->payment_status) {
          'paid'     => ['bg'=>'#f0fdf4','color'=>'#16a34a','border'=>'#bbf7d0'],
          'failed'   => ['bg'=>'#fff5f5','color'=>'#c00','border'=>'#fecaca'],
          'refunded' => ['bg'=>'#fafaf8','color'=>'#777','border'=>'#e8e4df'],
          default    => ['bg'=>'#fffbeb','color'=>'#92400e','border'=>'#fde68a'],
        };
      @endphp
      <span style="font-family:'Montserrat',sans-serif;display:inline-flex;align-items:center;gap:5px;font-size:0.62rem;font-weight:600;padding:5px 14px;letter-spacing:0.1em;text-transform:uppercase;
        background:{{ $pc['bg'] }};color:{{ $pc['color'] }};border:1px solid {{ $pc['border'] }}">
        @if($order->payment_status === 'paid')
          <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        @endif
        {{ $order->payment_status_label }}
      </span>
    </div>

    <div>
      <div style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;color:#aaa;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:8px">Método</div>
      <div style="font-family:'Montserrat',sans-serif;font-size:0.82rem;font-weight:500;color:#111">{{ $order->payment_method_label }}</div>
    </div>

  </div>

  @if($cardInfo)
  <div style="margin-top:20px;padding-top:20px;border-top:1px solid #e8e4df">
    <div style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;color:#aaa;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:12px">Cartão utilizado</div>
    <div style="display:inline-flex;align-items:center;gap:14px;background:#f5f0eb;border:1px solid #e8e4df;padding:12px 18px">
      <svg width="32" height="22" viewBox="0 0 32 22" fill="none" style="flex-shrink:0">
        <rect width="32" height="22" fill="#1a1a2e"/>
        <rect y="6" width="32" height="6" fill="#2d2d44"/>
        <rect x="4" y="15" width="10" height="3" fill="#f0f0f0" opacity=".6"/>
      </svg>
      <div>
        <div style="font-family:'Montserrat',sans-serif;font-size:0.82rem;font-weight:600;color:#111;letter-spacing:0.06em">
          {{ $cardInfo['brand'] }} •••• {{ $cardInfo['last4'] }}
        </div>
        <div style="font-family:'Montserrat',sans-serif;font-size:0.68rem;color:#888;margin-top:2px">
          Válido até {{ $cardInfo['exp_month'] }}/{{ $cardInfo['exp_year'] }}
        </div>
      </div>
    </div>
  </div>
  @endif
</div>

<div class="ac-card" style="margin-bottom:16px">
  <div class="ac-card-title">Itens do Pedido</div>

  @foreach($order->items as $item)
    <div style="display:flex;align-items:center;gap:16px;padding:16px 0;border-bottom:1px solid #f0ece8">
      @if($item->img)
        <img src="{{ asset($item->img) }}" style="width:64px;height:80px;object-fit:cover;background:#f5f0eb;flex-shrink:0" alt="">
      @else
        <div style="width:64px;height:80px;background:#f5f0eb;flex-shrink:0;display:flex;align-items:center;justify-content:center">
          <svg width="20" height="20" fill="none" stroke="#bbb" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18"/></svg>
        </div>
      @endif
      <div style="flex:1;min-width:0">
        <div style="font-family:'Montserrat',sans-serif;font-size:0.68rem;font-weight:500;color:#888;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:4px">{{ $item->product_brand }}</div>
        <div style="font-family:'Cormorant Garamond',serif;font-size:1.05rem;font-weight:500;color:#111;margin-bottom:6px">{{ $item->product_name }}</div>
        <div style="font-family:'Montserrat',sans-serif;font-size:0.7rem;color:#888;font-weight:400">Tam: {{ $item->size }} &bull; Qtd: {{ $item->qty }}</div>
      </div>
      <div style="font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:500;color:#111;flex-shrink:0">
        AED {{ number_format($item->price * $item->qty, 2, '.', ',') }}
      </div>
    </div>
  @endforeach

  <div style="margin-top:20px;display:flex;flex-direction:column;gap:8px;align-items:flex-end">
    <div style="font-family:'Montserrat',sans-serif;font-size:0.78rem;color:#777;font-weight:400">
      Frete: AED {{ number_format($order->shipping_cost, 2, '.', ',') }}
    </div>
    @if($order->discount > 0)
      <div style="font-family:'Montserrat',sans-serif;font-size:0.78rem;color:#16a34a;font-weight:500">
        Desconto: − AED {{ number_format($order->discount, 2, '.', ',') }}
      </div>
    @endif
    <div style="display:flex;align-items:baseline;gap:10px;padding-top:12px;border-top:1px solid #e8e4df;width:100%;justify-content:flex-end">
      <span style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;letter-spacing:0.14em;text-transform:uppercase;color:#888">Total</span>
      <span style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:500;color:#111">{{ $order->total_formatted }}</span>
    </div>
  </div>
</div>

@endsection
