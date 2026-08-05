@extends('layouts.checkout')
@section('title', 'Pagamento | Dias Sneakers')
@section('step1_class', 'done') @section('step1_num', '✓')
@section('step2_class', 'done') @section('step2_num', '✓')
@section('step3_class', 'done') @section('step3_num', '✓')
@section('step4_class', 'active') @section('step4_num', '4')
@section('content')

<div class="checkout-wrapper">

  {{-- ── COLUNA PRINCIPAL ── --}}
  <div class="checkout-main">

    <h2 class="co-section-heading">Forma de Pagamento</h2>

    <div class="pay-methods">

      {{-- CARTÃO --}}
      <div class="pay-method-card" id="mc-cartao" onclick="selectMethod('cartao')">
        <div class="pmc-header">
          <span class="pmc-radio" id="radio-cartao"></span>
          <span class="pmc-label">Credit Card</span>
          <span class="pmc-icons">
            <span class="card-flag-pill">VISA</span>
            <span class="card-flag-pill">MC</span>
            <span class="card-flag-pill">AMEX</span>
          </span>
        </div>
        <div class="pmc-body" id="body-cartao">
          <div class="card-preview" id="card-preview">
            <div class="card-chip"></div>
            <div class="card-number-display" id="cd-number">•••• •••• •••• ••••</div>
            <div class="card-bottom-row">
              <div><div class="card-label">Name</div><div class="card-holder-display" id="cd-name">YOUR NAME</div></div>
              <div><div class="card-label">Expiry</div><div class="card-exp-display" id="cd-exp">MM/YY</div></div>
            </div>
          </div>
          <div class="pmc-fields">
            <div class="pmc-field full">
              <label>Name on Card *</label>
              <input type="text" id="card-name" placeholder="AS SHOWN ON CARD" style="text-transform:uppercase" onclick="event.stopPropagation()">
            </div>
            <div class="pmc-field full">
              <label>Card Number *</label>
              <div id="stripe-card-number" class="stripe-element" onclick="event.stopPropagation()"></div>
            </div>
            <div class="pmc-field">
              <label>Expiry Date *</label>
              <div id="stripe-card-expiry" class="stripe-element" onclick="event.stopPropagation()"></div>
            </div>
            <div class="pmc-field">
              <label>CVV *</label>
              <div id="stripe-card-cvc" class="stripe-element" onclick="event.stopPropagation()"></div>
            </div>
            <div id="stripe-card-error" class="stripe-error" style="display:none"></div>
          </div>
        </div>
      </div>

      {{-- CASH ON DELIVERY --}}
      <div class="pay-method-card" id="mc-cod" onclick="selectMethod('cod')">
        <div class="pmc-header">
          <span class="pmc-radio" id="radio-cod"></span>
          <span class="pmc-label">Cash on Delivery</span>
          <span class="pmc-icons">
            <span class="card-flag-pill cod-pill">COD</span>
          </span>
        </div>
        <div class="pmc-body" id="body-cod" style="display:none">
          <div class="pix-info-row">
            <svg width="22" height="22" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Pay when your order arrives
          </div>
          <div class="pix-info-row">
            <svg width="22" height="22" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Cash payment to the Porter driver
          </div>
          <div class="pix-info-row">
            <svg width="22" height="22" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Please have the exact amount ready
          </div>
        </div>
      </div>

      {{-- DIGITAL WALLETS (shown only if Apple Pay / Google Pay is available) --}}
      <div class="pay-method-card" id="mc-wallet" onclick="selectMethod('wallet')" style="display:none">
        <div class="pmc-header">
          <span class="pmc-radio" id="radio-wallet"></span>
          <span class="pmc-label">Digital Wallets</span>
          <span class="pmc-icons">
            <span class="card-flag-pill wallet-pill">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
              Apple Pay
            </span>
            <span class="card-flag-pill wallet-pill">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#fff"/><path d="M21.8 12.2c0-.7-.1-1.3-.2-2H12v3.8h5.5c-.2 1.2-1 2.2-2 2.9v2.4h3.2c1.9-1.7 3.1-4.3 3.1-7.1z" fill="#4285F4"/><path d="M12 22c2.7 0 5-1 6.7-2.6l-3.2-2.4c-.9.6-2 1-3.5 1-2.7 0-5-1.8-5.8-4.3H2.9v2.5C4.6 19.9 8 22 12 22z" fill="#34A853"/><path d="M6.2 13.7c-.2-.6-.3-1.2-.3-1.7 0-.6.1-1.2.3-1.7V7.8H2.9C2.3 9 2 10.5 2 12s.3 3 .9 4.2l3.3-2.5z" fill="#FBBC05"/><path d="M12 5.7c1.5 0 2.8.5 3.9 1.5l2.9-2.9C17 2.8 14.7 2 12 2 8 2 4.6 4.1 2.9 7.8l3.3 2.5C7 7.5 9.3 5.7 12 5.7z" fill="#EA4335"/></svg>
              Google Pay
            </span>
          </span>
        </div>
        <div class="pmc-body" id="body-wallet" style="display:none">
          <div class="pix-info-row">
            <svg width="22" height="22" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Pay instantly with Face ID, Touch ID or fingerprint
          </div>
          <div class="pix-info-row">
            <svg width="22" height="22" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            No card details needed — 100% secure
          </div>
          <p style="font-size:.73rem;color:#aaa;margin-top:6px;text-align:center">Click "Place Order" below to open the payment sheet.</p>
        </div>
      </div>

    </div>

    {{-- BOTÃO FINALIZAR --}}
    <button class="co-btn-finalize" id="btn-finalize" onclick="finalize()">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      <span id="btn-label">Place Order</span>
    </button>
    <div class="co-secure">
      <svg width="13" height="13" fill="none" stroke="#999" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Pagamento 100% seguro — processado pelo Stripe
    </div>

  </div>

  {{-- ── SIDEBAR ── --}}
  <div class="checkout-sidebar">
    <div class="co-summary">
      <div class="co-summary-title">Resumo do Pedido</div>

      <div class="co-summary-items">
        @foreach($cart as $item)
        <div class="co-sum-item">
          <div class="co-sum-img-wrap">
            <img src="{{ asset($item['img']) }}" alt="{{ $item['product_name'] }}">
            <span class="co-sum-qty">{{ $item['qty'] }}</span>
          </div>
          <div class="co-sum-info">
            <div class="co-sum-brand">{{ $item['product_brand'] }}</div>
            <div class="co-sum-name">{{ $item['product_name'] }}</div>
            <div class="co-sum-meta">Tam: {{ $item['size'] }}</div>
          </div>
          <div class="co-sum-price">AED {{ number_format($item['price']*$item['qty'],2,'.',',') }}</div>
        </div>
        @endforeach
      </div>

      <div class="co-summary-rows">
        <div class="co-sum-row"><span>Subtotal</span><span>AED {{ number_format($subtotal,2,'.',',') }}</span></div>
        @if($discount>0)
        <div class="co-sum-row green"><span>Desconto</span><span>− AED {{ number_format($discount,2,'.',',') }}</span></div>
        @endif
        <div class="co-sum-row"><span>Frete</span><span>AED {{ number_format($shipping['cost']??0,2,'.',',') }}</span></div>
        <div class="co-sum-row total"><span>Total</span><span>AED {{ number_format($total,2,'.',',') }}</span></div>
      </div>
    </div>
  </div>

</div>

{{-- SUCCESS OVERLAY --}}
<div class="success-screen" id="success-screen" style="display:none">
  <div class="success-icon">
    <svg width="36" height="36" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
  </div>
  <h1 class="success-title">Pedido Realizado!</h1>
  <p class="success-sub">Você receberá a confirmação por e-mail em breve.</p>
  <div class="success-order-num">Pedido #<span id="success-num"></span></div>
  <a href="{{ route('home') }}" class="btn-success-home">Voltar para a Loja</a>
</div>


@endsection
@push('styles')
<style>
/* ── PAGAMENTO ── */
.co-section-heading {
  font-size: .9rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .08em;
  border-bottom: 2px solid #000;
  padding-bottom: 14px;
  margin-bottom: 20px;
}

.pay-methods { display: flex; flex-direction: column; gap: 0; }

.pay-method-card {
  border: 1.5px solid #e0e0e0;
  margin-bottom: -1px;
  cursor: pointer;
  transition: border-color .18s, box-shadow .18s;
  background: #fff;
  position: relative;
}
.pay-method-card.selected { border-color: #000; z-index: 1; box-shadow: 0 0 0 1px #000; }

.pmc-header { display: flex; align-items: center; gap: 14px; padding: 18px 20px; user-select: none; }

.pmc-radio {
  width: 20px; height: 20px; border-radius: 50%;
  border: 2px solid #ccc; flex-shrink: 0; position: relative;
  transition: border-color .18s;
}
.pmc-radio::after {
  content: ''; position: absolute; inset: 3px;
  border-radius: 50%; background: #000; opacity: 0; transition: opacity .18s;
}
.pay-method-card.selected .pmc-radio { border-color: #000; }
.pay-method-card.selected .pmc-radio::after { opacity: 1; }

.pmc-label { font-size: .88rem; font-weight: 700; flex: 1; }
.pmc-icons { display: flex; gap: 5px; align-items: center; }

.card-flag-pill {
  font-size: .6rem; font-weight: 800; letter-spacing: .05em;
  padding: 3px 7px; border: 1.5px solid #ddd; border-radius: 3px;
  color: #555; background: #fafafa;
}
.pix-pill    { border-color: #32bcad; color: #32bcad; background: #f0faf9; }
.cod-pill    { border-color: #888; color: #555; background: #f5f5f5; }
.wallet-pill { border-color: #c0c0c0; color: #444; background: #fafafa; display:inline-flex;align-items:center;gap:3px; }

.pmc-body { padding: 0 20px 24px; animation: fadeUp .2s ease; }

.pmc-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 4px; }
.pmc-field { display: flex; flex-direction: column; gap: 5px; }
.pmc-field.full { grid-column: 1 / -1; }
.pmc-field label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #666; }

.pmc-field input,
.pmc-field select {
  border: 1.5px solid #ddd;
  padding: 12px 14px;
  font-family: 'Inter', sans-serif;
  font-size: .88rem;
  outline: none;
  transition: border-color .18s;
  background: #fff;
  appearance: none;
}
.pmc-field input:focus, .pmc-field select:focus { border-color: #000; }

.pmc-field select {
  background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23999' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  cursor: pointer;
}

/* Stripe Elements */
.stripe-element {
  border: 1.5px solid #ddd;
  padding: 12px 14px;
  background: #fff;
  transition: border-color .18s;
  min-height: 44px;
}
.stripe-element.StripeElement--focus { border-color: #000; }
.stripe-element.StripeElement--invalid { border-color: #e55; }

.stripe-error {
  grid-column: 1 / -1;
  background: #fff5f5;
  color: #c53030;
  font-size: .78rem;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #fed7d7;
}

/* PIX / Boleto info rows */
.pix-info-row {
  display: flex; align-items: center; gap: 10px;
  font-size: .83rem; color: #444;
  padding: 8px 0; border-bottom: 1px solid #f5f5f5;
}
.pix-info-row:last-child { border-bottom: none; }

/* Boleto address section */
.boleto-address { margin-top: 16px; border-top: 1px solid #f0f0f0; padding-top: 16px; }
.boleto-address-title { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #888; margin: 0 0 4px; }

/* Botão */
.co-btn-finalize {
  width: 100%; background: #000; color: #fff;
  border: none; padding: 18px;
  font-family: 'Inter', sans-serif; font-size: .86rem;
  font-weight: 800; text-transform: uppercase; letter-spacing: .12em;
  cursor: pointer; margin-top: 28px;
  display: flex; align-items: center; justify-content: center; gap: 9px;
  transition: background .18s;
}
.co-btn-finalize:hover { background: #222; }
.co-btn-finalize:disabled { background: #aaa; cursor: not-allowed; }

.co-secure {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  font-size: .72rem; color: #aaa; margin-top: 12px;
}

/* Sidebar */
.co-summary { border: 1.5px solid #e0e0e0; background: #fafafa; }
.co-summary-title { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; padding: 18px 20px 14px; border-bottom: 1px solid #ebebeb; }
.co-summary-items { display: flex; flex-direction: column; padding: 14px 20px; gap: 14px; border-bottom: 1px solid #ebebeb; }
.co-sum-item { display: flex; gap: 12px; align-items: flex-start; }
.co-sum-img-wrap { position: relative; flex-shrink: 0; width: 64px; height: 64px; }
.co-sum-img-wrap img { width: 64px; height: 64px; object-fit: contain; background: #efefef; padding: 4px; }
.co-sum-qty { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; background: #888; color: #fff; border-radius: 50%; font-size: .66rem; font-weight: 800; display: flex; align-items: center; justify-content: center; }
.co-sum-info { flex: 1; min-width: 0; }
.co-sum-brand { font-size: .66rem; color: #aaa; text-transform: uppercase; letter-spacing: .06em; }
.co-sum-name  { font-size: .8rem; font-weight: 600; margin: 2px 0; line-height: 1.3; }
.co-sum-meta  { font-size: .7rem; color: #999; }
.co-sum-price { font-size: .82rem; font-weight: 700; white-space: nowrap; flex-shrink: 0; }
.co-summary-rows { padding: 14px 20px; display: flex; flex-direction: column; gap: 10px; }
.co-sum-row { display: flex; justify-content: space-between; font-size: .82rem; color: #666; }
.co-sum-row.green { color: #1a6b3a; font-weight: 600; }
.co-sum-row.total { font-size: 1rem; font-weight: 800; color: #000; padding-top: 12px; border-top: 1.5px solid #e0e0e0; margin-top: 4px; }
</style>
@endpush
@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const CSRF_TOKEN  = '{{ csrf_token() }}';
const STRIPE_KEY  = '{{ config("services.stripe.key") }}';
const PROCESS_URL = '{{ route("checkout.process") }}';
const CONFIRM_URL = '{{ route("checkout.confirm") }}';

const stripe   = Stripe(STRIPE_KEY);
const elements = stripe.elements();

const stripeStyle = {
  base: {
    fontSize: '14px',
    fontFamily: 'Inter, sans-serif',
    color: '#333',
    '::placeholder': { color: '#bbb' },
  },
  invalid: { color: '#e53e3e' },
};

const cardNumber = elements.create('cardNumber', { style: stripeStyle });
const cardExpiry = elements.create('cardExpiry', { style: stripeStyle });
const cardCvc    = elements.create('cardCvc',    { style: stripeStyle });

cardNumber.mount('#stripe-card-number');
cardExpiry.mount('#stripe-card-expiry');
cardCvc.mount('#stripe-card-cvc');

cardNumber.on('change', e => {
  const err = document.getElementById('stripe-card-error');
  if (e.error) { err.textContent = e.error.message; err.style.display = 'block'; }
  else         { err.style.display = 'none'; }
});

// ── PAYMENT REQUEST (Apple Pay / Google Pay) ──
const paymentRequest = stripe.paymentRequest({
  country:          'AE',
  currency:         'aed',
  total:            { label: 'Dias Sneakers', amount: {{ (int) round($total * 100) }} },
  requestPayerName: true,
  requestPayerEmail: true,
});

paymentRequest.canMakePayment().then(result => {
  if (result) document.getElementById('mc-wallet').style.display = 'block';
});

paymentRequest.on('paymentmethod', async (ev) => {
  const data = await postJson(PROCESS_URL, {
    payment_method:           'cartao',
    stripe_payment_method_id: ev.paymentMethod.id,
    card_name:                ev.payerName || '',
  });

  if (!data.success) { ev.complete('fail'); showError(data.message); return; }
  ev.complete('success');

  if (data.requires_action) {
    const { paymentIntent, error: actionError } = await stripe.handleNextAction({ clientSecret: data.client_secret });
    if (actionError) { showError(actionError.message); return; }
    const confirm = await postJson(CONFIRM_URL, { order_id: data.order_id, payment_intent_id: paymentIntent.id });
    if (confirm.success) window.location.href = confirm.redirect;
    else showError(confirm.message);
    return;
  }

  window.location.href = data.redirect;
});

let currentMethod = 'cartao';

function selectMethod(method) {
  ['cartao','cod','wallet'].forEach(m => {
    const mc = document.getElementById('mc-' + m);
    const bd = document.getElementById('body-' + m);
    if (mc) mc.classList.remove('selected');
    if (bd) bd.style.display = 'none';
  });
  document.getElementById('mc-' + method).classList.add('selected');
  document.getElementById('body-' + method).style.display = 'block';
  currentMethod = method;
}

selectMethod('cartao');

document.getElementById('card-name').addEventListener('input', function () {
  document.getElementById('cd-name').textContent = this.value.toUpperCase() || 'SEU NOME AQUI';
});

let processing = false;

async function finalize() {
  if (processing) return;

  if (currentMethod === 'wallet') {
    paymentRequest.show();
    return;
  }

  processing = true;
  const btn = document.getElementById('btn-finalize');
  btn.disabled = true;
  document.getElementById('btn-label').textContent = 'Processing...';

  try {
    if (currentMethod === 'cartao') {
      await processCard();
    } else {
      await processCod();
    }
  } catch (err) {
    showError(err.message || 'Unexpected error. Please try again.');
  } finally {
    processing = false;
    btn.disabled = false;
    document.getElementById('btn-label').textContent = 'Place Order';
  }
}

async function processCard() {
  const name = document.getElementById('card-name').value.trim();
  if (!name) { showError('Informe o nome no cartão.'); return; }

  const { paymentMethod, error } = await stripe.createPaymentMethod({
    type: 'card',
    card: cardNumber,
    billing_details: { name },
  });

  if (error) { showError(error.message); return; }

  const data = await postJson(PROCESS_URL, {
    payment_method:           'cartao',
    stripe_payment_method_id: paymentMethod.id,
    card_name:                name,
  });

  if (!data.success) { showError(data.message); return; }

  if (data.requires_action) {
    const { paymentIntent, error: actionError } = await stripe.handleNextAction({ clientSecret: data.client_secret });
    if (actionError) { showError(actionError.message); return; }

    const confirm = await postJson(CONFIRM_URL, {
      order_id:          data.order_id,
      payment_intent_id: paymentIntent.id,
    });

    if (confirm.success) window.location.href = confirm.redirect;
    else showError(confirm.message);
    return;
  }

  window.location.href = data.redirect;
}

async function processCod() {
  const data = await postJson(PROCESS_URL, { payment_method: 'cod' });
  if (!data.success) { showError(data.message); return; }
  window.location.href = data.redirect;
}

async function postJson(url, payload) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
    body: JSON.stringify(payload),
  });
  return res.json();
}

function showError(msg) {
  alert(msg);
}

</script>
@endpush
