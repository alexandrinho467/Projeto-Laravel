@extends('layouts.admin')
@section('title', 'Equipe | Admin')
@section('content')

<div class="admin-topbar">
  <h1 class="admin-title">Equipe</h1>
  <a href="{{ route('admin.team.create') }}" class="btn-primary">+ Novo membro</a>
</div>

@if($team->isEmpty())
  <div style="text-align:center;padding:60px;color:#697386">
    <div style="font-size:2rem;margin-bottom:12px">👥</div>
    <div style="font-weight:600;color:#374151">Nenhum membro da equipe ainda</div>
    <a href="{{ route('admin.team.create') }}" class="btn-primary" style="margin-top:20px;display:inline-block">Adicionar primeiro membro</a>
  </div>
@else
<table class="admin-table">
  <thead>
    <tr>
      <th>Nome</th>
      <th>E-mail</th>
      <th>Papel</th>
      <th>Desde</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($team as $member)
    <tr>
      <td>{{ $member->name }}</td>
      <td style="color:#697386">{{ $member->email }}</td>
      <td>
        @if($member->role === 'admin')
          <span class="badge badge-blue">Admin</span>
        @else
          <span class="badge badge-green">Vendedor</span>
        @endif
      </td>
      <td style="color:#697386;font-size:.85rem">{{ $member->created_at->format('d/m/Y') }}</td>
      <td>
        <div style="display:flex;gap:8px;align-items:center">
          <a href="{{ route('admin.team.edit', $member) }}" class="btn-secondary" style="padding:6px 12px;font-size:.78rem">Editar</a>

          @if($member->id !== auth()->id())
          <form action="{{ route('admin.team.destroy', $member) }}" method="POST"
            onsubmit="return confirm('Remover o acesso de {{ $member->name }}?')">
            @csrf @method('DELETE')
            <button class="btn-danger" style="padding:6px 12px;font-size:.78rem">Remover</button>
          </form>
          @endif
        </div>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif

@endsection
