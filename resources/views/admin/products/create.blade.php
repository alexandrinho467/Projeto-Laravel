@extends('layouts.admin')
@section('title', 'Novo Produto | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Novo Produto</h1>
  <a href="{{ route('admin.produtos.index') }}" class="btn-secondary">← Voltar</a>
</div>

<form action="{{ route('admin.produtos.store') }}" method="POST" enctype="multipart/form-data" style="max-width:800px">
  @csrf
  @include('admin.products._form')
  <button type="submit" class="btn-primary" style="margin-top:32px">Criar Produto</button>
</form>
@endsection
