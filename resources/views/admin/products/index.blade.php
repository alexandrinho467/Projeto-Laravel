@extends('layouts.admin')
@section('title', 'Produtos | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Produtos</h1>
  <a href="{{ route('admin.produtos.create') }}" class="btn-primary">+ Novo Produto</a>
</div>

<table class="admin-table">
  <thead>
    <tr><th>Foto</th><th>Produto</th><th>Marca</th><th>Preço</th><th>Tamanhos</th><th>Status</th><th>Ações</th></tr>
  </thead>
  <tbody>
    @forelse($products as $product)
      @php $img = $product->images->first(); @endphp
      <tr>
        <td><img src="{{ asset($img ? $img->img1 : '') }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px" alt=""></td>
        <td>
          <div style="font-weight:600;font-size:.9rem">{{ $product->name }}</div>
          @if($product->badge)<div style="font-size:.75rem;color:#635BFF;font-weight:500">{{ $product->badge }}</div>@endif
        </td>
        <td style="color:#697386;font-size:.88rem">{{ $product->brand }}</td>
        <td>
          <div style="font-weight:600">{{ $product->price_formatted }}</div>
          @if($product->old_price)<div style="text-decoration:line-through;color:#666;font-size:.78rem">{{ $product->old_price_formatted }}</div>@endif
        </td>
        <td style="font-size:.82rem;color:#888">{{ $product->sizes_count }}</td>
        <td>
          <button onclick="toggleActive({{ $product->id }}, this)"
                  style="background:{{ $product->active ? '#D1FAE5' : '#FEE2E2' }};color:{{ $product->active ? '#065F46' : '#991B1B' }};border:none;padding:4px 12px;border-radius:20px;font-size:.74rem;font-weight:600;cursor:pointer">
            {{ $product->active ? 'Ativo' : 'Inativo' }}
          </button>
        </td>
        <td>
          <a href="{{ route('admin.produtos.edit', $product) }}" class="btn-secondary" style="padding:6px 12px;font-size:.8rem">Editar</a>
          <form action="{{ route('admin.produtos.destroy', $product) }}" method="POST" style="display:inline" onsubmit="return confirm('Remover produto?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger" style="padding:6px 12px;font-size:.8rem">Remover</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" style="text-align:center;color:#666;padding:32px">Nenhum produto cadastrado.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection
@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
function toggleActive(id, btn) {
  fetch(`/admin/produtos/${id}/toggle`, {method:'POST',headers:{'X-CSRF-TOKEN':CSRF_TOKEN}})
    .then(r=>r.json()).then(d=>{
      btn.style.background = d.active ? '#D1FAE5' : '#FEE2E2';
      btn.style.color = d.active ? '#065F46' : '#991B1B';
      btn.textContent = d.active ? 'Ativo' : 'Inativo';
    });
}
</script>
@endpush
