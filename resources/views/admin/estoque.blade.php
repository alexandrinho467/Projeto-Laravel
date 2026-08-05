@extends('layouts.admin')
@section('title', 'Estoque | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Estoque</h1>
  <a href="{{ route('admin.produtos.create') }}" class="btn-primary">+ Cadastrar Produto</a>
</div>

<table class="admin-table">
  <thead>
    <tr>
      <th>Foto</th>
      <th>Produto</th>
      <th>Marca</th>
      <th>Preço</th>
      <th>Tamanhos disponíveis</th>
      <th>Status</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @forelse($products as $product)
      @php
        $img = $product->images->first();
        $sizesDisp = $product->sizes->where('available', true)->pluck('size');
        $sizesIndisp = $product->sizes->where('available', false)->pluck('size');
      @endphp
      <tr>
        <td>
          <img src="{{ asset($img ? $img->img1 : '') }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px" alt="">
        </td>
        <td>
          <div style="font-weight:600;font-size:.9rem">{{ $product->name }}</div>
          @if($product->ref)<div style="font-size:.75rem;color:#666">Ref: {{ $product->ref }}</div>@endif
        </td>
        <td style="color:#888;font-size:.88rem">{{ $product->brand }}</td>
        <td style="font-weight:600">{{ $product->price_formatted }}</td>
        <td>
          <div style="display:flex;flex-wrap:wrap;gap:4px">
            @forelse($product->sizes->sortBy('size') as $ps)
              @php
                $bg    = $ps->stock > 1 ? '#D1FAE5' : ($ps->stock === 1 ? '#FEF3C7' : '#F3F4F6');
                $color = $ps->stock > 1 ? '#065F46' : ($ps->stock === 1 ? '#92400E' : '#9CA3AF');
                $line  = $ps->stock === 0 ? 'text-decoration:line-through' : '';
              @endphp
              <span style="background:{{ $bg }};color:{{ $color }};padding:2px 8px;border-radius:6px;font-size:.72rem;font-weight:600;{{ $line }}" title="{{ $ps->stock }} par(es)">
                {{ $ps->size }} <span style="opacity:.7">({{ $ps->stock }})</span>
              </span>
            @empty
              <span style="color:#666;font-size:.8rem">—</span>
            @endforelse
          </div>
        </td>
        <td>
          <span style="background:{{ $product->active ? '#D1FAE5' : '#FEE2E2' }};color:{{ $product->active ? '#065F46' : '#991B1B' }};padding:3px 10px;border-radius:20px;font-size:.74rem;font-weight:600">
            {{ $product->active ? 'Ativo' : 'Inativo' }}
          </span>
        </td>
        <td>
          <div style="display:flex;gap:8px">
            <a href="{{ route('admin.estoque.edit', $product) }}" class="btn-primary" style="padding:6px 12px;font-size:.8rem">Gerenciar</a>
            <a href="{{ route('admin.produtos.edit', $product) }}" class="btn-secondary" style="padding:6px 12px;font-size:.8rem">Produto</a>
          </div>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" style="text-align:center;color:#666;padding:32px">Nenhum produto cadastrado.</td></tr>
    @endforelse
  </tbody>
</table>

<div style="margin-top:16px;font-size:.78rem;color:#697386;display:flex;gap:16px;align-items:center;flex-wrap:wrap">
  <span><span style="background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:6px;font-size:.72rem;font-weight:600">42 (2)</span> 2+ pares</span>
  <span><span style="background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:6px;font-size:.72rem;font-weight:600">42 (1)</span> Último par</span>
  <span><span style="background:#F3F4F6;color:#9CA3AF;padding:2px 8px;border-radius:6px;font-size:.72rem;font-weight:600;text-decoration:line-through">42 (0)</span> Esgotado</span>
</div>
@endsection
