@extends('layouts.app')
@section('title', $titulo . ' | Dias Sneakers')
@section('content')

<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a>
  <span class="bc-sep">›</span>
  <span class="bc-current">{{ $titulo }}</span>
</div>

<section class="products-section" style="padding-top: 56px;">
  <div class="section-header">
    <h1 class="section-title">{{ $titulo }}</h1>
    <p class="section-subtitle">{{ $products->count() }} produto{{ $products->count() !== 1 ? 's' : '' }}</p>
  </div>

  <div class="products-grid">
    @forelse($products as $product)
      @php $img = $product->images->first(); @endphp
      <div class="product-card">
        <a class="card-img-link" href="{{ route('product.show', $product->id) }}">
          <div class="product-image-wrap">
            @if($product->badge)
              <div class="product-badge-wrap">
                <span class="product-badge">{{ $product->badge }}</span>
              </div>
            @endif
            <img class="img-main" src="{{ asset($img ? $img->img1 : '') }}" alt="{{ $product->name }}" loading="lazy">
            <img class="img-hover" src="{{ asset($img ? ($img->img2 ?? $img->img1) : '') }}" alt="{{ $product->name }}" loading="lazy">
          </div>
        </a>
        <div class="product-info">
          <a class="card-name-link" href="{{ route('product.show', $product->id) }}">
            <div class="product-brand">{{ $product->brand }}</div>
            <div class="product-name">{{ $product->name }}</div>
          </a>
          <div class="product-price">{{ $product->price_formatted }}</div>
          <div class="sizes" id="sizes-{{ $product->id }}">
            @foreach($product->sizes as $sz)
              <div class="size-box {{ !$sz->available ? 'esgotado' : '' }}"
                   @if($sz->available) onclick="selectSize({{ $product->id }}, '{{ $sz->size }}', this)" @endif>
                {{ $sz->size }}
              </div>
            @endforeach
          </div>
          <button class="add-to-cart-btn" onclick="addToCart({{ $product->id }})">Adicionar ao Carrinho</button>
        </div>
      </div>
    @empty
      <p style="grid-column:1/-1; text-align:center; padding:72px 0; color:#aaa; font-size:0.78rem; letter-spacing:0.14em; text-transform:uppercase;">
        Nenhum produto encontrado nesta categoria.
      </p>
    @endforelse
  </div>
</section>

@endsection

@push('scripts')
<script>
const selectedSizes = {};

function selectSize(productId, size, el) {
  selectedSizes[productId] = size;
  document.querySelectorAll('#sizes-' + productId + ' .size-box:not(.esgotado)').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
}

function addToCart(productId) {
  const size = selectedSizes[productId];
  if (!size) { showToast('Selecione um tamanho primeiro'); return; }

  fetch(ROUTES.cartAdd, {
    method: 'POST',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body: JSON.stringify({product_id: productId, size: size})
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      updateCartBadge(d.count);
      loadCartPanel();
      showToast('Produto adicionado ao carrinho!');
      const panel = document.getElementById('cart-panel');
      const overlay = document.getElementById('cart-overlay');
      if (panel) panel.classList.add('open');
      if (overlay) overlay.classList.add('open');
    }
  });
}
</script>
@endpush
