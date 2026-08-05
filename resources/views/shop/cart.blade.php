@extends('layouts.checkout')
@section('title', 'Carrinho | Dias Sneakers')
@section('step1_class', 'active')
@section('step1_num', '1')
@section('step2_num', '2')
@section('step3_num', '3')
@section('step4_num', '4')
@section('content')

<div class="checkout-wrapper" id="checkout-wrapper">
  <div class="checkout-main">
    <div class="checkout-section-title">Meu Carrinho</div>

    <div id="cart-items-list">
      @forelse($cart as $key => $item)
        <div class="cart-item-row" id="row-{{ $key }}">
          <img src="{{ asset($item['img']) }}" alt="{{ $item['product_name'] }}">
          <div class="cart-item-info">
            <div class="ci-brand">{{ $item['product_brand'] }}</div>
            <div class="ci-name">{{ $item['product_name'] }}</div>
            <div class="ci-size">Tam: {{ $item['size'] }}</div>
          </div>
          <div class="cart-item-qty">
            <button onclick="changeQty('{{ $key }}', {{ $item['qty'] - 1 }})">-</button>
            <span id="qty-{{ $key }}">{{ $item['qty'] }}</span>
            <button onclick="changeQty('{{ $key }}', {{ $item['qty'] + 1 }})">+</button>
          </div>
          <div class="ci-price" id="price-{{ $key }}">AED {{ number_format($item['price'] * $item['qty'], 2, '.', ',') }}</div>
          <button class="remove-item" onclick="removeItem('{{ $key }}')">&#x2715;</button>
        </div>
      @empty
        <div class="cart-empty-msg">
          <p>Seu carrinho está vazio.</p>
          <a href="{{ route('home') }}" class="btn-checkout-continue" style="text-align:center;display:block;max-width:200px;margin:16px auto">Ver Produtos</a>
        </div>
      @endforelse
    </div>

    @if(count($cart) > 0)
    <div class="coupon-section">
      <div class="coupon-title">Cupom de Desconto</div>
      <div class="coupon-row">
        <input type="text" id="coupon-input" placeholder="DIGITE SEU CUPOM" value="{{ $coupon['code'] ?? '' }}">
        <button id="coupon-btn" onclick="applyCoupon()">Aplicar</button>
      </div>
      <div class="coupon-feedback" id="coupon-feedback"></div>
    </div>
    @endif
  </div>

  <div class="checkout-sidebar">
    <div class="order-summary">
      <div class="order-summary-title">Resumo do Pedido</div>
      <div class="summary-row"><span>Subtotal</span><span id="sum-subtotal">AED {{ number_format($subtotal,2,'.',',') }}</span></div>
      @if($discount > 0)
      <div class="summary-row discount" id="sum-discount-row">
        <span>Desconto (cupom)</span><span id="sum-discount">- AED {{ number_format($discount,2,'.',',') }}</span>
      </div>
      @endif
      <div class="summary-row"><span>Frete</span><span id="sum-shipping">Calcule abaixo</span></div>
      <div class="summary-row total"><span>Total</span><span id="sum-total">AED {{ number_format($total,2,'.',',') }}</span></div>

      <div class="summary-cep-section">
        <div class="porter-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          Entrega Expressa via Porter
        </div>
        <div class="summary-cep-label">Seu endereço</div>
        <div class="summary-cep-row">
          <input type="text" id="address-input" placeholder="Ex: Downtown Dubai, Marina..." style="flex:1">
          <button id="cep-btn">OK</button>
        </div>
        <div id="porter-distance" style="display:none;font-size:.7rem;color:#888;margin-top:4px;text-align:center"></div>
        <div id="shipping-opts"></div>
      </div>

      @if(count($cart) > 0)
        <div id="cep-error" style="display:none;color:#c00;font-size:.75rem;margin-bottom:8px;text-align:center"></div>
        <button class="btn-checkout-continue" onclick="goToCheckout()">Continuar</button>
      @endif

      <div class="secure-badge">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Compra 100% segura
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';

function removeItem(key) {
  fetch('{{ route('cart.remove') }}', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
    body: JSON.stringify({key})
  }).then(r=>r.json()).then(() => location.reload());
}

function changeQty(key, qty) {
  if (qty < 1) { removeItem(key); return; }
  fetch('{{ route('cart.qty') }}', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
    body: JSON.stringify({key, qty})
  }).then(r=>r.json()).then(() => location.reload());
}

function applyCoupon() {
  const code = document.getElementById('coupon-input').value;
  fetch('{{ route('cart.coupon') }}', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
    body: JSON.stringify({code})
  }).then(r=>r.json()).then(d => {
    const fb = document.getElementById('coupon-feedback');
    fb.textContent = d.message;
    fb.style.color = d.success ? '#4ade80' : '#f87171';
    if (d.success) setTimeout(() => location.reload(), 800);
  });
}

let shippingSelected = false;

document.getElementById('cep-btn').addEventListener('click', calcularFrete);
document.getElementById('address-input').addEventListener('keydown', e => { if (e.key === 'Enter') calcularFrete(); });

async function calcularFrete() {
  const endereco = document.getElementById('address-input').value.trim();
  const errEl    = document.getElementById('cep-error');
  const el       = document.getElementById('shipping-opts');
  const distEl   = document.getElementById('porter-distance');

  if (endereco.length < 3) {
    errEl.textContent = 'Digite seu endereço em Dubai.';
    errEl.style.display = 'block';
    return;
  }

  errEl.style.display = 'none';
  distEl.style.display = 'none';
  el.innerHTML = '<small style="color:#888">Consultando Porter...</small>';
  shippingSelected = false;

  try {
    const res  = await fetch('{{ route("cart.frete") }}', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
      body:    JSON.stringify({ endereco }),
    });
    const data = await res.json();

    if (!data.success || !data.opcoes?.length) {
      el.innerHTML = '';
      errEl.textContent = data.message || 'Não foi possível calcular o frete. Tente incluir o bairro (ex: Downtown Dubai, Marina...).';
      errEl.style.display = 'block';
      return;
    }

    if (data.distancia) {
      let info = `📍 ${data.distancia} km`;
      if (data.tempo) info += ` · ⏱ ~${Math.round(data.tempo)} min`;
      distEl.textContent = info;
      distEl.style.display = 'block';
    }

    el.innerHTML = '<div class="shipping-opts-list">' + data.opcoes.map(o => {
      const valor = o.valor.toFixed(2);
      return `<div class="shipping-option" onclick="selectShipping('${o.nome}',${o.valor},'${o.label}',this)">
        <div class="ship-radio"></div>
        <div class="ship-info">
          <div class="ship-name">${o.icone} ${o.label}</div>
          <div class="ship-days">Entrega Expressa — ${o.prazo}</div>
        </div>
        <span class="ship-price">AED ${valor}</span>
      </div>`;
    }).join('') + '</div>';

  } catch {
    el.innerHTML = '';
    errEl.textContent = 'Erro de conexão. Tente novamente.';
    errEl.style.display = 'block';
  }
}

function selectShipping(method, cost, label, el) {
  document.querySelectorAll('.shipping-option').forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  shippingSelected = true;
  document.getElementById('cep-error').style.display = 'none';
  fetch('{{ route('checkout.shipping') }}', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
    body: JSON.stringify({method, cost})
  });
  document.getElementById('sum-shipping').textContent = 'AED ' + cost.toFixed(2);
  const sub  = parseFloat('{{ $subtotal }}');
  const disc = parseFloat('{{ $discount }}');
  document.getElementById('sum-total').textContent = 'AED ' + (sub - disc + cost).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function goToCheckout() {
  const endereco = document.getElementById('address-input').value.trim();
  const errEl    = document.getElementById('cep-error');
  if (!endereco) {
    errEl.textContent = 'Informe seu endereço em Dubai para calcular o frete.';
    errEl.style.display = 'block';
    document.getElementById('address-input').focus();
    return;
  }
  if (!shippingSelected) {
    errEl.textContent = 'Selecione uma opção de entrega Porter para continuar.';
    errEl.style.display = 'block';
    return;
  }
  window.location.href = '{{ route('checkout.identification') }}';
}
</script>
@endpush
