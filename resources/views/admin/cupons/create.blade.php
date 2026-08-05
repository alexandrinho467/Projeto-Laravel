@extends('layouts.admin')
@section('title', 'Novo Cupom | Admin')
@section('content')

<div class="admin-topbar">
  <h1 class="admin-title">Novo cupom</h1>
  <a href="{{ route('admin.cupons.index') }}" class="btn-secondary">← Voltar</a>
</div>

@if($errors->any())
  <div class="alert-error">{{ $errors->first() }}</div>
@endif

<div style="max-width:600px">
  <form action="{{ route('admin.cupons.store') }}" method="POST">
    @csrf

    <div class="form-row">
      <div class="form-group">
        <label>Código do cupom *</label>
        <input type="text" name="code" value="{{ old('code') }}" placeholder="EX: PROMO10" required
          style="text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
      </div>
      <div class="form-group">
        <label>Tipo de desconto *</label>
        <select name="type" id="type" onchange="updateLabel()">
          <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Percentual (%)</option>
          <option value="fixed"   {{ old('type') === 'fixed'   ? 'selected' : '' }}>Valor fixo (AED)</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label id="value-label">Valor do desconto *</label>
        <input type="number" name="value" value="{{ old('value') }}" placeholder="0" step="0.01" min="0.01" required>
      </div>
      <div class="form-group">
        <label>Pedido mínimo (AED)</label>
        <input type="number" name="min_order" value="{{ old('min_order') }}" placeholder="0,00" step="0.01" min="0">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Limite de usos</label>
        <input type="number" name="max_uses" value="{{ old('max_uses') }}" placeholder="Deixe vazio = ilimitado" min="1">
      </div>
      <div class="form-group">
        <label>Data de validade</label>
        <input type="date" name="expires_at" value="{{ old('expires_at') }}">
      </div>
    </div>

    <div class="form-group" style="display:flex;align-items:center;gap:10px">
      <input type="hidden" name="active" value="0">
      <input type="checkbox" name="active" id="active" value="1" style="width:auto"
        {{ old('active', '1') == '1' ? 'checked' : '' }}>
      <label for="active" style="margin:0;cursor:pointer">Cupom ativo</label>
    </div>

    <div style="display:flex;gap:12px;margin-top:8px">
      <button type="submit" class="btn-primary">Criar cupom</button>
      <a href="{{ route('admin.cupons.index') }}" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
function updateLabel() {
  const type = document.getElementById('type').value;
  document.getElementById('value-label').textContent =
    type === 'percent' ? 'Percentual de desconto (%) *' : 'Valor do desconto (AED) *';
}
updateLabel();
</script>
@endpush
@endsection
