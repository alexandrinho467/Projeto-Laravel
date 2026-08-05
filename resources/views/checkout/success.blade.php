@extends('layouts.checkout')
@section('title', 'Pedido Confirmado | Dias Sneakers')
@section('step1_class', 'done') @section('step1_num', '✓')
@section('step2_class', 'done') @section('step2_num', '✓')
@section('step3_class', 'done') @section('step3_num', '✓')
@section('step4_class', 'done') @section('step4_num', '✓')
@section('content')

<div style="max-width:540px;margin:60px auto;text-align:center;padding:0 20px">
  <div style="width:70px;height:70px;background:#16a34a;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px">
    <svg width="36" height="36" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
  </div>
  <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:12px;color:#111">Pedido Realizado!</h1>
  <p style="color:#666;margin-bottom:24px">Seu pedido foi recebido com sucesso.<br>Você receberá a confirmação por e-mail em breve.</p>
  <div style="background:#111;border-radius:12px;padding:24px;margin-bottom:32px;text-align:left;color:#fff">
    <div style="font-size:.8rem;color:#888;margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em">Pedido</div>
    <div style="font-size:1.5rem;font-weight:800;margin-bottom:20px;color:#fff">#{{ $order->id }}</div>
    @foreach($order->items as $item)
    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #2a2a2a;font-size:.88rem;color:#ccc">
      <span>{{ $item->product_brand }} {{ $item->product_name }} ({{ $item->size }}) x{{ $item->qty }}</span>
      <span>AED {{ number_format($item->price*$item->qty,2,'.',',') }}</span>
    </div>
    @endforeach
    <div style="display:flex;justify-content:space-between;margin-top:16px;font-weight:700;color:#fff">
      <span>Total</span><span>{{ $order->total_formatted }}</span>
    </div>
    <div style="margin-top:8px;font-size:.82rem;color:#888">
      Pagamento: {{ ucfirst($order->payment_method) }} &bull; {{ $order->payment_status_label }}
    </div>
  </div>
  <a href="{{ route('home') }}" class="btn-checkout-continue" style="display:inline-block;text-decoration:none;text-align:center">Voltar para a Loja</a>
  @auth
    <a href="{{ route('account.orders') }}" style="display:block;margin-top:12px;color:#f97316;font-size:.88rem">Ver meus pedidos</a>
  @endauth
</div>

@endsection
