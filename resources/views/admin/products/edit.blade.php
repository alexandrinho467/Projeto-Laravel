@extends('layouts.admin')
@section('title', 'Editar Produto | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Editar: {{ $product->name }}</h1>
  <a href="{{ route('admin.produtos.index') }}" class="btn-secondary">← Voltar</a>
</div>

<form action="{{ route('admin.produtos.update', $product) }}" method="POST" enctype="multipart/form-data" style="max-width:800px">
  @csrf @method('PUT')
  @include('admin.products._form', ['product' => $product])
  <button type="submit" class="btn-primary" style="margin-top:32px">Salvar Alterações</button>
</form>
@endsection
