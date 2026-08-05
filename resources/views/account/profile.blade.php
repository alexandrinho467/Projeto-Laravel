@extends('layouts.account')
@section('title', 'Meus Dados | Dias Sneakers')
@section('content')

<div class="ac-page-title">Meus Dados</div>
<div class="ac-page-subtitle">Gerencie suas informações pessoais e senha.</div>

<div class="ac-card">
  <div class="ac-card-title">Informações Pessoais</div>
  <form action="{{ route('account.profile.update') }}" method="POST">
    @csrf

    @if($errors->any())
      <div class="ac-alert-error" style="margin-bottom:24px">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
      </div>
    @endif

    <div class="ac-form-grid">
      <div class="ac-form-group">
        <label for="name">Nome completo *</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Seu nome">
      </div>
      <div class="ac-form-group">
        <label for="email">E-mail *</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="seu@email.com">
      </div>
      <div class="ac-form-group">
        <label for="id_document">ID / Emirates ID</label>
        <input id="id_document" type="text" name="id_document" value="{{ old('id_document', $user->id_document) }}" maxlength="30" placeholder="784-XXXX-XXXXXXX-X">
      </div>
      <div class="ac-form-group">
        <label for="pcd-number">Phone</label>
        @include('partials.phone-input', ['value' => old('phone', $user->phone ?? ''), 'name' => 'phone', 'id' => 'phone'])
      </div>
      <div class="ac-form-group">
        <label for="birth_date">Nascimento</label>
        <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
      </div>
    </div>

    <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e5e5">
      <div class="ac-card-title" style="margin-bottom:16px">Endereço Padrão de Entrega</div>

      @if($user->address_street)
        <div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#15803d">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          Default address: {{ $user->address_street }}, {{ $user->address_number }}
          @if($user->address_complement) — {{ $user->address_complement }}@endif
          · {{ $user->address_neighborhood }}, {{ $user->address_city }}, {{ $user->address_state }}
        </div>
      @endif

      <div class="ac-form-grid">
        <div class="ac-form-group">
          <label for="address_cep">Flat / Villa No</label>
          <input id="address_cep" type="text" name="address_cep" value="{{ old('address_cep', $user->address_cep) }}" placeholder="Ex: Apt 204, Villa 12" maxlength="30">
        </div>
        <div class="ac-form-group">
          <label for="address_number">Building Name</label>
          <input id="address_number" type="text" name="address_number" value="{{ old('address_number', $user->address_number) }}" placeholder="Ex: Marina Crown Tower">
        </div>
      </div>

      <div class="ac-form-grid" style="margin-top:16px">
        <div class="ac-form-group">
          <label for="address_street">Street / Road</label>
          <input id="address_street" type="text" name="address_street" value="{{ old('address_street', $user->address_street) }}" placeholder="Ex: Sheikh Zayed Road">
        </div>
        <div class="ac-form-group">
          <label for="address_neighborhood">Area / District</label>
          <input id="address_neighborhood" type="text" name="address_neighborhood" value="{{ old('address_neighborhood', $user->address_neighborhood) }}" placeholder="Ex: Downtown Dubai, Marina, JBR">
        </div>
        <div class="ac-form-group">
          <label for="address_complement">Additional Directions</label>
          <input id="address_complement" type="text" name="address_complement" value="{{ old('address_complement', $user->address_complement) }}" placeholder="Ex: Near Dubai Mall, Blue building">
        </div>
        <div class="ac-form-group">
          <label for="address_city">City</label>
          <input id="address_city" type="text" name="address_city" value="{{ old('address_city', $user->address_city ?? 'Dubai') }}" placeholder="Dubai">
        </div>
        <div class="ac-form-group">
          <label for="address_state">Emirate</label>
          <select id="address_state" name="address_state">
            <option value="">Select</option>
            @foreach(['Dubai','Abu Dhabi','Sharjah','Ajman','Ras Al Khaimah','Fujairah','Umm Al Quwain'] as $emirate)
              <option value="{{ $emirate }}" {{ old('address_state', $user->address_state) === $emirate ? 'selected' : '' }}>{{ $emirate }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e5e5">
      <div class="ac-card-title" style="margin-bottom:16px">Alterar Senha</div>
      <p style="font-size:.82rem;color:#888;margin-bottom:20px">Deixe em branco caso não queira alterar a senha.</p>
      <div class="ac-form-grid">
        <div class="ac-form-group">
          <label for="password">Nova Senha</label>
          <input id="password" type="password" name="password" placeholder="Mínimo 8 caracteres">
        </div>
        <div class="ac-form-group">
          <label for="password_confirmation">Confirmar Senha</label>
          <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repita a senha">
        </div>
      </div>
    </div>

    <div style="margin-top:28px">
      <button type="submit" class="ac-btn ac-btn-primary">Salvar Alterações</button>
    </div>
  </form>
</div>

@endsection
