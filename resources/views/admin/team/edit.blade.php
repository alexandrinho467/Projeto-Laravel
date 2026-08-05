@extends('layouts.admin')
@section('title', 'Editar Membro | Admin')
@section('content')

<div class="admin-topbar">
  <h1 class="admin-title">Editar {{ $member->name }}</h1>
  <a href="{{ route('admin.team.index') }}" class="btn-secondary">← Voltar</a>
</div>

@if($errors->any())
  <div class="alert-error">{{ $errors->first() }}</div>
@endif

<div style="max-width:600px">
  <form action="{{ route('admin.team.update', $member) }}" method="POST">
    @csrf @method('PUT')

    <div class="form-group">
      <label>Nome *</label>
      <input type="text" name="name" value="{{ old('name', $member->name) }}" required>
    </div>

    <div class="form-group">
      <label>E-mail *</label>
      <input type="email" name="email" value="{{ old('email', $member->email) }}" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Nova senha</label>
        <input type="password" name="password" placeholder="Deixe em branco para manter a senha atual">
      </div>
      <div class="form-group">
        <label>Confirmar nova senha</label>
        <input type="password" name="password_confirmation">
      </div>
    </div>

    <div class="form-group">
      <label>Papel *</label>
      <select name="role" required>
        <option value="vendedor" {{ old('role', $member->role) === 'vendedor' ? 'selected' : '' }}>Vendedor (acesso só ao CRM)</option>
        <option value="admin" {{ old('role', $member->role) === 'admin' ? 'selected' : '' }}>Admin (acesso total)</option>
      </select>
    </div>

    <div style="display:flex;gap:12px;margin-top:8px">
      <button type="submit" class="btn-primary">Salvar alterações</button>
      <a href="{{ route('admin.team.index') }}" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

@endsection
