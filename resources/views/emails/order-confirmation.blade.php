<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido confirmado</title>
  <style>
    body { margin: 0; padding: 0; background: #f5f5f5; font-family: Arial, sans-serif; color: #111; }
    .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }

    /* Header */
    .header { background: #111; padding: 28px 40px; }
    .header-inner { display: flex; align-items: center; justify-content: space-between; }
    .logo { font-size: 1.1rem; font-weight: 800; letter-spacing: .04em; color: #fff; }
    .logo span { color: #f97316; }
    .order-num { font-size: .78rem; color: #888; }

    /* Hero */
    .hero { background: #f97316; padding: 28px 40px; text-align: center; }
    .hero-icon { font-size: 2.2rem; margin-bottom: 8px; }
    .hero-title { font-size: 1.3rem; font-weight: 800; color: #fff; margin: 0 0 4px; }
    .hero-sub { font-size: .85rem; color: rgba(255,255,255,.85); margin: 0; }

    /* Body */
    .body { padding: 32px 40px; }
    .greeting { font-size: .95rem; color: #333; margin-bottom: 24px; line-height: 1.6; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 28px; }
    .info-card { background: #f9f9f9; border-radius: 6px; padding: 14px 16px; }
    .info-card .label { font-size: .7rem; color: #999; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
    .info-card .value { font-size: .9rem; font-weight: 700; color: #111; }

    /* Produtos */
    .section-title { font-size: .75rem; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 12px; }
    .item-row { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
    .item-row:last-child { border-bottom: none; }
    .item-img { width: 56px; height: 56px; object-fit: cover; border-radius: 6px; background: #f5f5f5; flex-shrink: 0; }
    .item-img-placeholder { width: 56px; height: 56px; border-radius: 6px; background: #f0f0f0; flex-shrink: 0; }
    .item-info { flex: 1; }
    .item-brand { font-size: .72rem; color: #999; margin-bottom: 2px; }
    .item-name { font-size: .88rem; font-weight: 700; color: #111; margin-bottom: 2px; }
    .item-detail { font-size: .75rem; color: #aaa; }
    .item-price { font-size: .9rem; font-weight: 700; color: #111; white-space: nowrap; }

    /* Totais */
    .totals { background: #f9f9f9; border-radius: 6px; padding: 16px 20px; margin-top: 20px; }
    .total-row { display: flex; justify-content: space-between; font-size: .85rem; color: #666; margin-bottom: 8px; }
    .total-row:last-child { margin-bottom: 0; border-top: 1px solid #e8e8e8; padding-top: 10px; margin-top: 4px; }
    .total-row.final { font-size: 1rem; font-weight: 800; color: #111; }

    /* Pagamento */
    .payment-box { margin-top: 24px; padding: 16px 20px; border: 1px solid #f0f0f0; border-radius: 6px; display: flex; align-items: center; gap: 14px; }
    .payment-icon { font-size: 1.6rem; flex-shrink: 0; }
    .payment-label { font-size: .72rem; color: #999; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
    .payment-method { font-size: .9rem; font-weight: 700; color: #111; }
    .payment-info { font-size: .78rem; color: #aaa; margin-top: 2px; }

    /* Footer */
    .footer { background: #f5f5f5; padding: 20px 40px; text-align: center; font-size: .72rem; color: #aaa; border-top: 1px solid #eee; line-height: 1.7; }
    .footer a { color: #f97316; text-decoration: none; }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- Header -->
  <div class="header">
    <div class="header-inner">
      <div class="logo">Dias <span>Sneakers</span></div>
      <div class="order-num">Pedido #{{ $order->id }}</div>
    </div>
  </div>

  <!-- Hero -->
  <div class="hero">
    <div class="hero-icon">✓</div>
    <p class="hero-title">Pedido confirmado!</p>
    <p class="hero-sub">Recebemos seu pagamento e já estamos preparando seu pedido.</p>
  </div>

  <!-- Body -->
  <div class="body">

    <p class="greeting">
      Olá, <strong>{{ $order->guest_name }}</strong>!<br>
      Seu pedido foi confirmado em <strong>{{ $order->created_at->format('d/m/Y \à\s H:i') }}</strong>.
      Abaixo estão todos os detalhes da sua compra.
    </p>

    <!-- Info cards -->
    <div class="info-grid">
      <div class="info-card">
        <div class="label">Número do pedido</div>
        <div class="value">#{{ $order->id }}</div>
      </div>
      <div class="info-card">
        <div class="label">Data do pedido</div>
        <div class="value">{{ $order->created_at->format('d/m/Y') }}</div>
      </div>
      <div class="info-card">
        <div class="label">Status</div>
        <div class="value" style="color:#f97316">{{ $order->status_label }}</div>
      </div>
      <div class="info-card">
        <div class="label">Frete</div>
        <div class="value">
          {{ $order->shipping_method ?? 'PAC' }}
          @if($order->shipping_cost > 0)
            — AED {{ number_format($order->shipping_cost, 2, '.', ',') }}
          @else
            — Grátis
          @endif
        </div>
      </div>
    </div>

    <!-- Produtos -->
    <div class="section-title">Itens do pedido</div>

    @foreach($order->items as $item)
    <div class="item-row">
      @if($item->img)
        <img src="{{ asset($item->img) }}" alt="{{ $item->product_name }}" class="item-img">
      @else
        <div class="item-img-placeholder"></div>
      @endif
      <div class="item-info">
        <div class="item-brand">{{ $item->product_brand }}</div>
        <div class="item-name">{{ $item->product_name }}</div>
        <div class="item-detail">Tamanho: {{ $item->size }} &nbsp;·&nbsp; Qtd: {{ $item->qty }}</div>
      </div>
      <div class="item-price">AED {{ number_format($item->price * $item->qty, 2, '.', ',') }}</div>
    </div>
    @endforeach

    <!-- Totais -->
    <div class="totals">
      <div class="total-row">
        <span>Subtotal</span>
        <span>AED {{ number_format($order->subtotal, 2, '.', ',') }}</span>
      </div>
      @if($order->discount > 0)
      <div class="total-row">
        <span>Desconto @if($order->coupon_code)({{ $order->coupon_code }})@endif</span>
        <span style="color:#16a34a">− AED {{ number_format($order->discount, 2, '.', ',') }}</span>
      </div>
      @endif
      <div class="total-row">
        <span>Frete</span>
        <span>{{ $order->shipping_cost > 0 ? 'AED ' . number_format($order->shipping_cost, 2, '.', ',') : 'Grátis' }}</span>
      </div>
      <div class="total-row final">
        <span>Total</span>
        <span>AED {{ number_format($order->total, 2, '.', ',') }}</span>
      </div>
    </div>

    <!-- Forma de pagamento -->
    <div class="payment-box">
      @php
        $icons  = ['cartao' => '💳', 'pix' => '⚡', 'boleto' => '📄'];
        $icon   = $icons[$order->payment_method] ?? '💰';
        $labels = ['cartao' => 'Cartão de crédito', 'pix' => 'PIX', 'boleto' => 'Boleto bancário'];
        $label  = $labels[$order->payment_method] ?? $order->payment_method;
        $infos  = [
          'cartao' => 'Pagamento processado via Stripe com segurança.',
          'pix'    => 'Pagamento PIX confirmado instantaneamente.',
          'boleto' => 'Boleto pago e confirmado.',
        ];
        $info = $infos[$order->payment_method] ?? '';
      @endphp
      <div class="payment-icon">{{ $icon }}</div>
      <div>
        <div class="payment-label">Forma de pagamento</div>
        <div class="payment-method">{{ $label }}</div>
        <div class="payment-info">{{ $info }}</div>
      </div>
    </div>

  </div>

  <!-- Footer -->
  <div class="footer">
    Dúvidas? Entre em contato conosco pelo WhatsApp ou responda este e-mail.<br>
    <strong>Dias Sneakers</strong> — Obrigado pela sua compra!
  </div>

</div>
</body>
</html>
