@extends('layouts.admin')
@section('title', 'Novo Membro | Admin')
@section('content')

<div class="admin-topbar">
  <h1 class="admin-title">Novo membro da equipe</h1>
  <a href="{{ route('admin.team.index') }}" class="btn-secondary">← Voltar</a>
</div>

@if($errors->any())
  <div class="alert-error">{{ $errors->first() }}</div>
@endif

<div style="max-width:600px">
  <form action="{{ route('admin.team.store') }}" method="POST">
    @csrf

    <div class="form-group">
      <label>Nome *</label>
      <input type="text" name="name" value="{{ old('name') }}" required>
    </div>

    <div class="form-group">
      <label>E-mail *</label>
      <input type="email" name="email" value="{{ old('email') }}" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Senha *</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>Confirmar senha *</label>
        <input type="password" name="password_confirmation" required>
      </div>
    </div>

    <div class="form-group">
      <label>Papel *</label>
      <select name="role" required>
        <option value="vendedor" {{ old('role') === 'vendedor' ? 'selected' : '' }}>Vendedor (acesso só ao CRM)</option>
        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (acesso total)</option>
      </select>
    </div>

    <div style="display:flex;gap:12px;margin-top:8px">
      <button type="submit" class="btn-primary">Adicionar membro</button>
      <a href="{{ route('admin.team.index') }}" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<div style="max-width:600px;margin-top:36px;padding-top:28px;border-top:1px solid #E3E8EE">
  <h2 style="font-size:1rem;font-weight:700;margin-bottom:6px;color:#1A1F36">Ou promova uma conta já existente</h2>
  <p style="color:#697386;font-size:.85rem;margin-bottom:18px">Se a pessoa já tem cadastro como cliente no site, use o e-mail dela aqui em vez de criar um cadastro novo — assim ela mantém a mesma senha e login.</p>

  <form action="{{ route('admin.team.promote') }}" method="POST">
    @csrf

    <div class="form-group">
      <label>E-mail da conta existente *</label>
      <input type="email" name="email" value="{{ old('email') }}" placeholder="email@cliente.com" required>
    </div>

    <div class="form-group">
      <label>Papel *</label>
      <select name="role" required>
        <option value="vendedor">Vendedor (acesso só ao CRM)</option>
        <option value="admin">Admin (acesso total)</option>
      </select>
    </div>

    <button type="submit" class="btn-secondary">Promover para a equipe</button>
  </form>
</div>

@endsection
