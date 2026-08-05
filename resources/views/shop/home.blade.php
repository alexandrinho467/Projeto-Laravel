@extends('layouts.app')
@section('title', 'Dias Sneakers | Tênis de Alta Performance')
@section('content')

<section class="banner-main">
  <img src="{{ asset($settings['banner_img']) }}" alt="Dias Sneakers">

  <div class="banner-left-overlay">
    <span class="blo-season">Nova Temporada 2025</span>
    <h2 class="blo-title">{{ $settings['banner_title'] }}</h2>
    <p class="blo-desc">{{ $settings['banner_subtitle'] }}</p>
    <a href="{{ route('masculino') }}" class="blo-cta">Explorar Coleção</a>

    <div class="blo-coupon">
      <span class="blo-coupon-code">BEMVINDO10</span>
      <span class="blo-coupon-label">cupom de boas vindas</span>
    </div>
  </div>
</section>

<section class="brand-video-section">
  <div class="brand-video-side">
    <video autoplay loop muted playsinline>
      <source src="{{ asset('assets/images/tenis-360.mp4') }}" type="video/mp4">
    </video>
  </div>
  <div class="brand-promo-side">
    <span class="brand-promo-label">Marca em Destaque</span>
    <h2 class="brand-promo-name">BALMAIN</h2>
    <p class="brand-promo-desc">Alta costura francesa que encontra a energia da rua. Descubra a coleção de tênis Balmain — onde o luxo se une à performance.</p>
    <a href="{{ route('marca.show', 'balmain') }}" class="brand-promo-btn">Ver Coleção Balmain</a>
  </div>
</section>

<div class="impact-phrase">
  <span class="impact-line">"Cada passo é uma declaração.</span>
  <span class="impact-line impact-line--light">Onde a performance encontra o luxo."</span>
</div>

<div class="photo-gallery-row">
  <a href="{{ route('marca.show', 'mizuno') }}" class="gallery-item" id="gallery-mizuno">
    <img src="{{ asset('assets/images/banner-mizuno.jpg') }}" alt="Mizuno">
  </a>

  <div class="gallery-item gallery-item--middle" id="gallery-middle">
    <div class="gallery-cta" id="cta-mizuno">
      <span class="gcta-label">Marca</span>
      <span class="gcta-brand">Mizuno</span>
      <div class="gcta-line"></div>
      <a href="{{ route('marca.show', 'mizuno') }}" class="gcta-btn">Ver Coleção</a>
    </div>
    <div class="gallery-cta" id="cta-airmax">
      <span class="gcta-label">Coleção</span>
      <span class="gcta-brand">Air Max's</span>
      <div class="gcta-line"></div>
      <a href="{{ route('marca.show', 'nike') }}" class="gcta-btn">Ver Coleção</a>
    </div>
  </div>

  <a href="{{ route('marca.show', 'nike') }}" class="gallery-item" id="gallery-airmax">
    <img src="{{ asset('assets/images/banner-airmax-95.jpg') }}" alt="Nike Air Max 95">
  </a>
</div>

<div class="editorial-divider">
  <span class="divider-text">Destaques da Temporada</span>
</div>

<section class="products-section">
  <div class="products-grid products-grid--preview">
    @foreach($products as $product)
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
    @endforeach
  </div>
</section>

<div class="ver-todos-wrap">
  <button class="ver-todos-btn" id="ver-todos-btn">Ver Todos os Produtos</button>
</div>

<div class="secondary-banner-wrap">
  <a href="{{ route('marca.show', 'new-balance') }}" class="secondary-banner-link">
    <img src="{{ asset('assets/images/banner-new-balance.jpg') }}" alt="New Balance">
  </a>
  <div class="secondary-banner-cta">
    <span class="gcta-label">Marca</span>
    <span class="gcta-brand">New Balance</span>
    <div class="gcta-line"></div>
    <a href="{{ route('marca.show', 'new-balance') }}" class="gcta-btn">Ver Coleção</a>
  </div>
</div>

{{-- Painel com todos os produtos --}}
<div class="all-products-overlay" id="all-products-overlay"></div>
<div class="all-products-panel" id="all-products-panel">
  <div class="all-products-header">
    <h2 class="all-products-title">Todos os Produtos</h2>
    <button class="all-products-close" id="close-all-products">&#x2715;</button>
  </div>
  <div class="products-grid all-products-grid">
    @foreach($products as $product)
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
          <div class="sizes" id="psizes-{{ $product->id }}">
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
    @endforeach
  </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
  const verTodosBtn = document.getElementById('ver-todos-btn');
  const panel       = document.getElementById('all-products-panel');
  const overlay     = document.getElementById('all-products-overlay');
  const closeBtn    = document.getElementById('close-all-products');

  function openPanel() {
    panel.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closePanel() {
    panel.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (verTodosBtn) verTodosBtn.addEventListener('click', openPanel);
  if (closeBtn)    closeBtn.addEventListener('click', closePanel);
  if (overlay)     overlay.addEventListener('click', closePanel);
})();
</script>
<script>
(function () {
  const mizunoItem = document.getElementById('gallery-mizuno');
  const airmaxItem = document.getElementById('gallery-airmax');
  const middle     = document.getElementById('gallery-middle');
  const ctaMizuno  = document.getElementById('cta-mizuno');
  const ctaAirmax  = document.getElementById('cta-airmax');

  if (!mizunoItem || !airmaxItem) return;

  let hideTimer = null;

  function showCta(cta) {
    clearTimeout(hideTimer);
    ctaMizuno.classList.remove('active');
    ctaAirmax.classList.remove('active');
    cta.classList.add('active');
  }

  function scheduleHide() {
    hideTimer = setTimeout(() => {
      ctaMizuno.classList.remove('active');
      ctaAirmax.classList.remove('active');
    }, 150);
  }

  function hideCta() {
    clearTimeout(hideTimer);
    ctaMizuno.classList.remove('active');
    ctaAirmax.classList.remove('active');
  }

  mizunoItem.addEventListener('mouseenter', () => showCta(ctaMizuno));
  mizunoItem.addEventListener('mouseleave', scheduleHide);

  airmaxItem.addEventListener('mouseenter', () => showCta(ctaAirmax));
  airmaxItem.addEventListener('mouseleave', scheduleHide);

  middle.addEventListener('mouseenter', () => clearTimeout(hideTimer));
  middle.addEventListener('mouseleave', hideCta);
})();
</script>
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
