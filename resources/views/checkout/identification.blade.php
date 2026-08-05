@extends('layouts.checkout')
@section('title', 'Identificação | Dias Sneakers')
@section('step1_class', 'done') @section('step1_num', '✓')
@section('step2_class', 'active') @section('step2_num', '2')
@section('step3_num', '3') @section('step4_num', '4')
@section('content')

<div class="checkout-wrapper">
  <div class="checkout-main">

    @auth
    <div class="checkout-section-title">Você está logado como {{ auth()->user()->name }}</div>
    @else
    <div class="checkout-section-title">Acesso</div>
    <div class="id-blocks">
      <div class="id-block">
        <div class="id-block-title">Já tenho conta</div>
        <div class="id-block-sub">Entre com seu e-mail e senha para continuar.</div>
        <div class="form-field"><label>E-mail</label><input type="email" id="login-email" placeholder="seu@email.com"></div>
        <div class="form-field"><label>Senha</label><input type="password" id="login-senha" placeholder="••••••••"></div>
        <button class="btn-id-enter" onclick="doLogin()">Entrar</button>
        <div id="login-error" style="color:#f87171;font-size:.82rem;margin-top:8px"></div>
      </div>
      <div class="id-block">
        <div class="id-block-title">Continuar como visitante</div>
        <div class="id-block-sub">Sem necessidade de criar uma conta.</div>
        <div class="form-field"><label>E-mail</label><input type="email" id="guest-email" placeholder="seu@email.com"></div>
        <button class="btn-id-enter" style="margin-top:54px" onclick="fillGuestEmail()">Continuar como Visitante</button>
      </div>
    </div>
    @endauth

    <form action="{{ route('checkout.identification.save') }}" method="POST" id="id-form">
      @csrf
      <div class="checkout-section-title" style="margin-top:32px">Dados Pessoais</div>

      @if($errors->any())
        <div style="background:#450a0a;color:#f87171;padding:12px;border-radius:8px;margin-bottom:16px;font-size:.85rem">
          @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
      @endif

      <div class="form-field">
        <label>Nome Completo *</label>
        <input type="text" name="name" value="{{ old('name', auth()->user()?->name ?? '') }}" placeholder="Digite seu nome completo" required>
      </div>
      <div class="form-row">
        <div class="form-field">
          <label>E-mail *</label>
          <input type="email" name="email" id="field-email" value="{{ old('email', auth()->user()?->email ?? '') }}" placeholder="your@email.com" required>
        </div>
        <div class="form-field">
          <label>ID / Emirates ID *</label>
          <input type="text" name="id_document" value="{{ old('id_document', auth()->user()?->id_document ?? '') }}" placeholder="784-XXXX-XXXXXXX-X" maxlength="30" required>
        </div>
      </div>
      <div class="form-field" style="max-width:260px">
        <label>Phone *</label>
        @include('partials.phone-input', ['value' => old('phone', auth()->user()?->phone ?? ''), 'name' => 'phone', 'id' => 'phone', 'required' => true])
      </div>

      <button type="submit" class="btn-checkout-continue" style="margin-top:28px">Continuar</button>
    </form>
  </div>

  <div class="checkout-sidebar">
    <div class="mini-summary">
      <div class="mini-summary-title">Resumo do Pedido</div>
      @foreach($cart as $item)
      <div style="display:flex;justify-content:space-between;font-size:.85rem;padding:8px 0;border-bottom:1px solid #1f1f1f">
        <span>{{ $item['product_name'] }} ({{ $item['size'] }}) x{{ $item['qty'] }}</span>
        <span>AED {{ number_format($item['price']*$item['qty'],2,'.',',') }}</span>
      </div>
      @endforeach
      <div style="display:flex;justify-content:space-between;margin-top:12px;font-weight:700">
        <span>Total</span><span>AED {{ number_format($total,2,'.',',') }}</span>
      </div>
    </div>
  </div>
</div>

@endsection
@push('styles')
<style>
.cpf-feedback{display:block;font-size:.75rem;margin-top:4px;font-weight:600}
.cpf-ok{color:#1a6b3a}.cpf-err{color:#c00}
</style>
@endpush
@push('scripts')
<script>
function doLogin() {
  fetch('{{ route('login.post') }}', {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
    body: JSON.stringify({email:document.getElementById('login-email').value, password:document.getElementById('login-senha').value})
  }).then(r => { if(r.redirected) location.reload(); else r.json().then(d => { document.getElementById('login-error').textContent = d.message || 'Credenciais inválidas'; }); });
}
function fillGuestEmail() {
  const em = document.getElementById('guest-email').value;
  if (!em) { alert('Informe seu e-mail'); return; }
  document.getElementById('field-email').value = em;
  document.getElementById('id-form').scrollIntoView({behavior:'smooth'});
}
</script>
@endpush
