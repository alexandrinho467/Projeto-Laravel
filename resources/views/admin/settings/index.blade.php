@extends('layouts.admin')
@section('title', 'Configurações | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Configurações do Site</h1>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" style="max-width:700px">
  @csrf @method('POST')

  <div style="background:#111;border-radius:12px;padding:28px;margin-bottom:20px">
    <h3 style="margin:0 0 20px;font-size:1rem">Geral</h3>
    <div class="form-group">
      <label>Nome do Site</label>
      <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Dias Sneakers' }}">
    </div>
    <div class="form-group">
      <label>E-mail de Suporte</label>
      <input type="email" name="site_email" value="{{ $settings['site_email'] ?? '' }}">
    </div>
    <div class="form-group">
      <label>Frete Grátis acima de (AED)</label>
      <input type="number" name="free_shipping_min" value="{{ $settings['free_shipping_min'] ?? '1500' }}">
    </div>
  </div>

  <div style="background:#111;border-radius:12px;padding:28px;margin-bottom:20px">
    <h3 style="margin:0 0 20px;font-size:1rem">Banner Principal</h3>
    <div class="form-group">
      <label>URL da Imagem do Banner</label>
      <input type="text" name="banner_img" value="{{ $settings['banner_img'] ?? '' }}" placeholder="https://...">
    </div>
    <div class="form-group">
      <label>Título</label>
      <input type="text" name="banner_title" value="{{ $settings['banner_title'] ?? '' }}">
    </div>
    <div class="form-group">
      <label>Subtítulo / Chamada</label>
      <input type="text" name="banner_subtitle" value="{{ $settings['banner_subtitle'] ?? '' }}">
    </div>
  </div>

  <div style="background:#111;border-radius:12px;padding:28px;margin-bottom:20px">
    <h3 style="margin:0 0 20px;font-size:1rem">Stripe</h3>
    <p style="font-size:.78rem;color:#666;margin-top:0;margin-bottom:16px">As chaves Stripe são configuradas no arquivo <code>.env</code> do servidor (STRIPE_KEY e STRIPE_SECRET). Use chaves <strong>pk_test_ / sk_test_</strong> para testes e <strong>pk_live_ / sk_live_</strong> para produção.</p>
  </div>

  <button type="submit" class="btn-primary">Salvar Configurações</button>
</form>
@endsection
