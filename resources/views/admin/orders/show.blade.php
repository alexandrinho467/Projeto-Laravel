@extends('layouts.admin')
@section('title', 'Pedido #' . $order->id . ' | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Pedido #{{ $order->id }}</h1>
  <a href="{{ route('admin.orders.index') }}" class="btn-secondary">← Voltar</a>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
  <div>
    <div style="background:#fff;border-radius:8px;border:1px solid #E3E8EE;padding:24px;margin-bottom:20px">
      <h3 style="margin:0 0 16px;font-size:1rem">Itens do Pedido</h3>
      @foreach($order->items as $item)
        <div style="display:flex;align-items:center;gap:14px;padding:10px 0;border-bottom:1px solid #F1F5F9">
          @if($item->img)<img src="{{ asset($item->img) }}" style="width:56px;height:56px;object-fit:cover;border-radius:6px" alt="">@endif
          <div style="flex:1">
            <div style="font-weight:600;font-size:.9rem">{{ $item->product_brand }} {{ $item->product_name }}</div>
            <div style="color:#697386;font-size:.8rem">Tam: {{ $item->size }} &bull; x{{ $item->qty }}</div>
          </div>
          <div style="font-weight:700">AED {{ number_format($item->price*$item->qty,2,'.',',') }}</div>
        </div>
      @endforeach
      <div style="text-align:right;margin-top:16px">
        @if($order->discount>0)<div style="color:#059669;font-size:.88rem">Desconto: -AED {{ number_format($order->discount,2,'.',',') }}</div>@endif
        <div style="font-size:.88rem;color:#697386">Frete: AED {{ number_format($order->shipping_cost,2,'.',',') }}</div>
        <div style="font-size:1.1rem;font-weight:800;margin-top:8px">Total: {{ $order->total_formatted }}</div>
      </div>
    </div>

    <div style="background:#fff;border-radius:8px;border:1px solid #E3E8EE;padding:24px">
      <h3 style="margin:0 0 16px;font-size:1rem">Dados do Cliente</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:.88rem">
        <div><span style="color:#697386">Nome:</span> {{ $order->guest_name }}</div>
        <div><span style="color:#697386">E-mail:</span> {{ $order->guest_email }}</div>
        <div><span style="color:#697386">ID:</span> {{ $order->guest_id_document }}</div>
        <div><span style="color:#697386">Phone:</span> {{ $order->guest_phone }}</div>
      </div>
    </div>
  </div>

  <div>
    <div style="background:#fff;border-radius:8px;border:1px solid #E3E8EE;padding:24px;margin-bottom:16px">
      <h3 style="margin:0 0 16px;font-size:1rem">Informações</h3>
      <div style="font-size:.85rem;line-height:2">
        <div>Pagamento: <strong>{{ ucfirst($order->payment_method) }}</strong></div>
        <div>Status pag.: <strong>{{ $order->payment_status_label }}</strong></div>
        @if($order->stripe_id)<div style="font-size:.78rem;color:#697386">Stripe: {{ $order->stripe_id }}</div>@endif
        <div>Frete: <strong>{{ $order->shipping_method }}</strong></div>
        @if($order->coupon_code)<div>Cupom: <strong>{{ $order->coupon_code }}</strong></div>@endif
      </div>
    </div>

    <div style="background:#fff;border-radius:8px;border:1px solid #E3E8EE;padding:24px">
      <h3 style="margin:0 0 16px;font-size:1rem">Atualizar Status</h3>
      <form action="{{ route('admin.orders.status', $order) }}" method="POST">
        @csrf
        <select name="status" class="form-group" style="display:block;width:100%;margin-bottom:12px">
          @foreach(['pending'=>'Aguardando','processing'=>'Em processamento','shipped'=>'Enviado','delivered'=>'Entregue','cancelled'=>'Cancelado'] as $val => $label)
            <option value="{{ $val }}" {{ $order->status===$val?'selected':'' }}>{{ $label }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn-primary" style="width:100%">Atualizar</button>
      </form>
    </div>
  </div>
</div>
@endsection
