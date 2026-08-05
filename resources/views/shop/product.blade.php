@extends('layouts.app')
@section('title', $product->name . ' | Dias Sneakers')
@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/produto.css') }}">
@endpush
@section('content')

<nav class="breadcrumb">
  <a href="{{ route('home') }}">Home</a>
  <span class="bc-sep">›</span>
  <span>{{ $product->brand }}</span>
  <span class="bc-sep">›</span>
  <span class="bc-current">{{ $product->name }}</span>
</nav>

<section class="product-page-section">
  <div class="product-gallery">
    <div class="thumb-strip" id="thumb-strip">
      @php $img = $product->images->first(); @endphp
      @if($img)
        <div class="thumb-item active" data-src="{{ asset($img->img1) }}">
          <img src="{{ asset($img->img1) }}" alt="Vista 1">
        </div>
        @if($img->img2 && $img->img2 !== $img->img1)
        <div class="thumb-item" data-src="{{ asset($img->img2) }}">
          <img src="{{ asset($img->img2) }}" alt="Vista 2">
        </div>
        @endif
      @endif
    </div>
    <div class="main-img-container" id="main-img-container">
      <img id="main-img" src="{{ asset($img ? $img->img1 : '') }}" alt="{{ $product->name }}">
    </div>
  </div>

  <div class="product-info-panel">
    @if($product->badge)
      <span class="product-badge">{{ $product->badge }}</span>
    @endif
    <div class="product-brand-label">{{ $product->brand }}</div>
    <h1 class="product-title">{{ $product->name }}</h1>

    <div class="price-block">
      @if($product->old_price)
        <div class="old-price">De: <s>{{ $product->old_price_formatted }}</s></div>
      @endif
      <div class="price-row">
        <span class="current-price">{{ $product->price_formatted }}</span>
        @if($product->discount_percent > 0)
          <span class="discount-tag">-{{ $product->discount_percent }}%</span>
        @endif
      </div>
      <div class="installment">ou <strong>12x</strong> de <strong>AED {{ number_format($product->price/12,2,'.',',') }}</strong> sem juros</div>
    </div>

    <div class="selector-group">
      <div class="selector-label">Cor: <strong id="color-name">{{ $img ? $img->color_name : '' }}</strong></div>
      <div class="color-swatches" id="color-swatches">
        @foreach($product->images as $ci => $color)
          <div class="color-swatch {{ $ci === 0 ? 'active' : '' }}" data-index="{{ $ci }}"
               data-img1="{{ asset($color->img1) }}" data-img2="{{ asset($color->img2 ?? $color->img1) }}"
               data-name="{{ $color->color_name }}" title="{{ $color->color_name }}">
            <img src="{{ asset($color->img1) }}" alt="{{ $color->color_name }}">
          </div>
        @endforeach
      </div>
    </div>

    <div class="selector-group">
      <div class="selector-label-row">
        <span>Tamanho:</span>
        <a href="#" class="size-guide-link" id="size-guide-link">Guia de tamanhos</a>
      </div>
      <div class="sizes" id="sizes-panel">
        @foreach($product->sizes as $sz)
          <div class="size-box {{ !$sz->available ? 'esgotado' : '' }}" data-size="{{ $sz->size }}">{{ $sz->size }}</div>
        @endforeach
      </div>
    </div>

    <div class="action-buttons">
      <button class="btn-buy-now" id="btn-buy-now">Comprar Agora</button>
      <div class="add-fav-row">
        <button class="btn-add-cart" id="btn-add-cart">Adicionar ao Carrinho</button>
        <button class="btn-fav" id="btn-fav" title="Favoritar">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
      </div>
      <p class="size-warning" id="size-warning" style="display:none">Selecione um tamanho</p>
    </div>

    <div class="delivery-section">
      <div class="cep-row">
        <input type="text" id="cep-input" placeholder="00000-000" maxlength="9">
        <button id="cep-btn">Calcular frete</button>
      </div>
      <div id="shipping-options"></div>
    </div>
  </div>
</section>

<section class="description-section">
  <div class="description-inner">
    <div class="description-col">
      <h2>Sobre o produto</h2>
      <p>{{ $product->description }}</p>
      <h3>Informações Técnicas</h3>
      <p>{{ $product->tech }}</p>
    </div>
    <div class="description-col">
      <h3>Principais Características</h3>
      <ul>
        @foreach($product->features as $feat)
          <li>{{ $feat->feature }}</li>
        @endforeach
      </ul>
      @if($product->ref)
        <div class="product-ref">Código: <strong>{{ $product->ref }}</strong></div>
      @endif
    </div>
  </div>
</section>

<section class="reviews-section">
  <div class="reviews-inner">

    {{-- Rating summary --}}
    <div class="reviews-header">
      <div class="reviews-title-block">
        <h2 class="reviews-heading">Avaliações</h2>
        @if($reviews->count() > 0)
          <div class="reviews-avg">
            @php $star = round($avgRating * 2) / 2; @endphp
            <span class="avg-number">{{ number_format($avgRating, 1, ',', '') }}</span>
            <div class="star-row">
              @for($s = 1; $s <= 5; $s++)
                @if($s <= floor($star))
                  <span class="star star--full">★</span>
                @elseif($s == ceil($star) && $star != floor($star))
                  <span class="star star--half">★</span>
                @else
                  <span class="star star--empty">★</span>
                @endif
              @endfor
            </div>
            <span class="reviews-count">{{ $reviews->count() }} {{ $reviews->count() == 1 ? 'avaliação' : 'avaliações' }}</span>
          </div>
        @endif
      </div>
    </div>

    {{-- Existing reviews --}}
    @if($reviews->count() > 0)
      <div class="reviews-list">
        @foreach($reviews as $rev)
          <div class="review-item">
            <div class="review-top">
              <div class="review-stars">
                @for($s = 1; $s <= 5; $s++)
                  <span class="star {{ $s <= $rev->rating ? 'star--full' : 'star--empty' }}">★</span>
                @endfor
              </div>
              <span class="review-name">{{ $rev->name }}</span>
              <span class="review-date">{{ $rev->created_at->format('d/m/Y') }}</span>
            </div>
            @if($rev->body)
              <p class="review-body">{{ $rev->body }}</p>
            @endif
            @php $rimgs = $rev->images(); @endphp
            @if(count($rimgs) > 0 || $rev->video)
              <div class="review-media">
                @foreach($rimgs as $rimg)
                  <a href="{{ asset($rimg) }}" target="_blank" class="review-img-wrap">
                    <img src="{{ asset($rimg) }}" alt="Foto da avaliação">
                  </a>
                @endforeach
                @if($rev->video)
                  <video class="review-video" controls>
                    <source src="{{ asset($rev->video) }}" type="video/mp4">
                  </video>
                @endif
              </div>
            @endif
            @if(auth()->check() && (auth()->id() === $rev->user_id || auth()->user()->isAdmin()))
              <div class="review-actions">
                <button type="button" class="rev-edit-btn"
                  onclick="openEditReview({{ $rev->id }}, {{ json_encode($rev->name) }}, {{ $rev->rating }}, {{ json_encode($rev->body ?? '') }})">
                  Editar
                </button>
                <form method="POST" action="{{ route('review.destroy', $rev->id) }}" style="display:inline"
                  onsubmit="return confirm('Apagar esta avaliação?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="rev-delete-btn">Apagar</button>
                </form>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <p class="reviews-empty">Ainda não há avaliações. Seja o primeiro a avaliar!</p>
    @endif

    {{-- Review form --}}
    <div class="review-form-wrap">
      <h3 class="review-form-title">Deixe sua avaliação</h3>

      @if(session('review_success'))
        <div class="review-success">{{ session('review_success') }}</div>
      @endif

      <form class="review-form" method="POST" action="{{ route('review.store', $product->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="rf-row">
          <label class="rf-label">Seu nome *</label>
          <input class="rf-input" type="text" name="name" value="{{ old('name') }}" required maxlength="80">
          @error('name')<span class="rf-error">{{ $message }}</span>@enderror
        </div>

        <div class="rf-row">
          <label class="rf-label">Nota *</label>
          <div class="star-selector" id="star-selector">
            @for($s = 1; $s <= 5; $s++)
              <span class="ss-star" data-val="{{ $s }}">★</span>
            @endfor
          </div>
          <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', 0) }}">
          @error('rating')<span class="rf-error">{{ $message }}</span>@enderror
        </div>

        <div class="rf-row">
          <label class="rf-label">Comentário <small>(máx. 100 palavras)</small></label>
          <textarea class="rf-textarea" name="body" id="review-body" rows="5" maxlength="800">{{ old('body') }}</textarea>
          <div class="word-counter"><span id="word-count">0</span> / 100 palavras</div>
          @error('body')<span class="rf-error">{{ $message }}</span>@enderror
        </div>

        <div class="rf-row">
          <label class="rf-label">Fotos <small>(até 3 imagens, max 5 MB cada)</small></label>
          <div class="rf-file-group">
            <label class="rf-file-btn">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
              Adicionar fotos
              <input type="file" name="images[]" id="img-input" accept="image/*" multiple style="display:none">
            </label>
            <div class="rf-previews" id="img-previews"></div>
          </div>
          @error('images.*')<span class="rf-error">{{ $message }}</span>@enderror
        </div>

        <div class="rf-row">
          <label class="rf-label">Vídeo <small>(mp4 / mov / webm, max 50 MB)</small></label>
          <div class="rf-file-group">
            <label class="rf-file-btn">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
              Adicionar vídeo
              <input type="file" name="video" id="video-input" accept="video/mp4,video/webm,video/quicktime" style="display:none">
            </label>
            <div id="video-preview"></div>
          </div>
          @error('video')<span class="rf-error">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="rf-submit">Enviar Avaliação</button>
      </form>
    </div>

  </div>
</section>

<section class="related-section">
  <h2 class="section-title">Você também pode gostar</h2>
  <div class="related-grid">
    @foreach($related as $r)
      @php $ri = $r->images->first(); @endphp
      <div class="product-card" onclick="window.location='{{ route('product.show', $r->id) }}'">
        <div class="product-image-wrap">
          <img class="img-main" src="{{ asset($ri ? $ri->img1 : '') }}" alt="{{ $r->name }}" loading="lazy">
          <img class="img-hover" src="{{ asset($ri ? ($ri->img2 ?? $ri->img1) : '') }}" alt="{{ $r->name }}" loading="lazy">
        </div>
        <div class="product-info">
          <div class="product-brand">{{ $r->brand }}</div>
          <div class="product-name">{{ $r->name }}</div>
          <div class="product-price">{{ $r->price_formatted }}</div>
        </div>
      </div>
    @endforeach
  </div>
</section>

<!-- Edit Review Modal -->
<div class="rev-modal-overlay" id="rev-edit-overlay" style="display:none" onclick="if(event.target===this)closeEditReview()">
  <div class="rev-modal">
    <div class="rev-modal-header">
      <h3>Editar Avaliação</h3>
      <button type="button" class="rev-modal-close" onclick="closeEditReview()">&#x2715;</button>
    </div>
    <form method="POST" id="rev-edit-form" action="">
      @csrf @method('PUT')
      <div class="rf-row">
        <label class="rf-label">Seu nome *</label>
        <input class="rf-input" type="text" name="name" id="edit-name" required maxlength="80">
      </div>
      <div class="rf-row">
        <label class="rf-label">Nota *</label>
        <div class="star-selector" id="edit-star-selector">
          @for($s = 1; $s <= 5; $s++)
            <span class="ss-star edit-star" data-val="{{ $s }}">★</span>
          @endfor
        </div>
        <input type="hidden" name="rating" id="edit-rating-input" value="0">
      </div>
      <div class="rf-row">
        <label class="rf-label">Comentário <small>(máx. 100 palavras)</small></label>
        <textarea class="rf-textarea" name="body" id="edit-body" rows="5" maxlength="800"></textarea>
      </div>
      <div class="rev-modal-footer">
        <button type="button" class="rev-cancel-btn" onclick="closeEditReview()">Cancelar</button>
        <button type="submit" class="rf-submit" style="margin:0">Salvar</button>
      </div>
    </form>
  </div>
</div>

<!-- Size guide modal -->
<div class="modal-overlay" id="modal-overlay">
  <div class="size-modal">
    <div class="modal-header"><h3>Guia de Tamanhos</h3><button class="close-modal" id="close-modal">&#x2715;</button></div>
    <table class="size-table">
      <thead><tr><th>BR</th><th>US</th><th>UK</th><th>EU</th><th>CM</th></tr></thead>
      <tbody>
        <tr><td>38</td><td>7</td><td>6</td><td>38</td><td>24,0</td></tr>
        <tr><td>39</td><td>8</td><td>7</td><td>39</td><td>25,0</td></tr>
        <tr><td>40</td><td>8,5</td><td>7,5</td><td>40</td><td>25,5</td></tr>
        <tr><td>41</td><td>9</td><td>8</td><td>41</td><td>26,0</td></tr>
        <tr><td>42</td><td>10</td><td>9</td><td>42</td><td>27,0</td></tr>
        <tr><td>43</td><td>11</td><td>10</td><td>43</td><td>28,0</td></tr>
        <tr><td>44</td><td>12</td><td>11</td><td>44</td><td>29,0</td></tr>
      </tbody>
    </table>
  </div>
</div>

@endsection
@push('scripts')
<script>
let selectedSize = null;
const productId = {{ $product->id }};
const productPrice = {{ $product->price }};

// Thumb strip
document.querySelectorAll('.thumb-item').forEach(el => {
  el.addEventListener('click', () => {
    document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('main-img').src = el.dataset.src;
  });
});

// Color swatches
document.querySelectorAll('.color-swatch').forEach(el => {
  el.addEventListener('click', () => {
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('color-name').textContent = el.dataset.name;
    document.getElementById('main-img').src = el.dataset.img1;
    // Update thumbs
    const strip = document.getElementById('thumb-strip');
    strip.innerHTML = '';
    [[el.dataset.img1,'Vista 1'],[el.dataset.img2,'Vista 2']].forEach(([src,alt],i) => {
      if (!src || (i===1 && src===el.dataset.img1)) return;
      const d = document.createElement('div');
      d.className = 'thumb-item' + (i===0?' active':'');
      d.dataset.src = src;
      d.innerHTML = '<img src="'+src+'" alt="'+alt+'">';
      d.addEventListener('click', () => { document.querySelectorAll('.thumb-item').forEach(t=>t.classList.remove('active')); d.classList.add('active'); document.getElementById('main-img').src=src; });
      strip.appendChild(d);
    });
  });
});

// Size selection
document.querySelectorAll('#sizes-panel .size-box:not(.esgotado)').forEach(el => {
  el.addEventListener('click', () => {
    document.querySelectorAll('#sizes-panel .size-box').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    selectedSize = el.dataset.size;
    document.getElementById('size-warning').style.display = 'none';
  });
});

// Zoom
const cont = document.getElementById('main-img-container');
const mimg = document.getElementById('main-img');
cont.addEventListener('mouseenter', () => { mimg.style.transition='transform .3s'; mimg.style.transform='scale(2.4)'; });
cont.addEventListener('mousemove', e => {
  const r = cont.getBoundingClientRect();
  mimg.style.transition='none';
  mimg.style.transformOrigin = ((e.clientX-r.left)/r.width*100).toFixed(1)+'% '+((e.clientY-r.top)/r.height*100).toFixed(1)+'%';
});
cont.addEventListener('mouseleave', () => { mimg.style.transition='transform .3s'; mimg.style.transform='scale(1)'; mimg.style.transformOrigin='50% 50%'; });

// Add to cart
function doAddToCart() {
  if (!selectedSize) { document.getElementById('size-warning').style.display='block'; return false; }
  return fetch(ROUTES.cartAdd, {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body: JSON.stringify({product_id: productId, size: selectedSize})
  }).then(r=>r.json()).then(d => {
    if (d.success) { updateCartBadge(d.count); loadCartPanel(); showToast('Produto adicionado!'); }
    return d.success;
  });
}

document.getElementById('btn-add-cart').addEventListener('click', doAddToCart);
document.getElementById('btn-buy-now').addEventListener('click', async () => {
  const ok = await doAddToCart();
  if (ok) window.location = '{{ route('cart') }}';
});

// CEP
document.getElementById('cep-input').addEventListener('input', function() {
  let v = this.value.replace(/\D/g,'');
  if (v.length > 5) v = v.slice(0,5)+'-'+v.slice(5,8);
  this.value = v;
});
document.getElementById('cep-btn').addEventListener('click', calcShipping);
document.getElementById('cep-input').addEventListener('keydown', e => { if(e.key==='Enter') calcShipping(); });

function calcShipping() {
  const cep = document.getElementById('cep-input').value.replace(/\D/g,'');
  if (cep.length < 8) { showToast('CEP inválido'); return; }
  const el = document.getElementById('shipping-options');
  el.innerHTML = '<div class="shipping-loading">Calculando...</div>';
  const isFree = productPrice >= 1500;
  setTimeout(() => {
    el.innerHTML = [
      {name:'PAC', days:'8 dias úteis', price: isFree?0:19.90},
      {name:'SEDEX', days:'3 dias úteis', price:34.90},
      {name:'SEDEX 10', days:'amanhã até 10h', price:59.90},
    ].map(s=>`<div class="shipping-option"><div class="ship-info"><span class="ship-name">${s.name}</span><span class="ship-days">Entrega em ${s.days}</span></div><span class="ship-price ${s.price===0?'gratis':''}">${s.price===0?'Grátis':'R$ '+s.price.toFixed(2).replace('.',',')}</span></div>`).join('');
  }, 600);
}

// Favoritar
(function () {
  const btn = document.getElementById('btn-fav');
  if (!btn) return;
  const FAVS_KEY = 'dias_favorites';

  function getFavs() {
    try { return JSON.parse(localStorage.getItem(FAVS_KEY) || '[]'); } catch { return []; }
  }
  function saveFavs(arr) { localStorage.setItem(FAVS_KEY, JSON.stringify(arr)); }
  function isFav(id) { return getFavs().includes(id); }

  function syncBtn(fav) {
    btn.classList.toggle('favorited', fav);
    btn.title = fav ? 'Remover dos favoritos' : 'Favoritar';
    const svg = btn.querySelector('svg');
    if (svg) {
      svg.style.fill   = fav ? '#000' : 'none';
      svg.style.stroke = '#000';
    }
  }

  syncBtn(isFav(productId));

  btn.addEventListener('click', function () {
    let favs = getFavs();
    const fav = favs.includes(productId);
    if (fav) {
      favs = favs.filter(id => id !== productId);
      showToast('Removido dos favoritos');
    } else {
      favs.push(productId);
      showToast('Adicionado aos favoritos!');
    }
    saveFavs(favs);
    syncBtn(!fav);
  });
})();

// Size guide modal
document.getElementById('size-guide-link').addEventListener('click', e => { e.preventDefault(); document.getElementById('modal-overlay').classList.add('open'); });
document.getElementById('close-modal').addEventListener('click', () => document.getElementById('modal-overlay').classList.remove('open'));
document.getElementById('modal-overlay').addEventListener('click', e => { if(e.target===e.currentTarget) e.currentTarget.classList.remove('open'); });

// ── Reviews ──────────────────────────────────────────────
(function () {
  // Star selector
  const stars = document.querySelectorAll('.ss-star');
  const ratingInput = document.getElementById('rating-input');
  let current = parseInt(ratingInput.value) || 0;

  function paintStars(val) {
    stars.forEach(s => {
      s.classList.toggle('active', parseInt(s.dataset.val) <= val);
    });
  }
  paintStars(current);

  stars.forEach(s => {
    s.addEventListener('mouseenter', () => paintStars(parseInt(s.dataset.val)));
    s.addEventListener('mouseleave', () => paintStars(current));
    s.addEventListener('click', () => {
      current = parseInt(s.dataset.val);
      ratingInput.value = current;
      paintStars(current);
    });
  });

  // Bloquear envio se nenhuma estrela selecionada
  document.querySelector('.review-form').addEventListener('submit', function (e) {
    if (parseInt(ratingInput.value) < 1) {
      e.preventDefault();
      ratingInput.closest('.rf-row').querySelector('.rf-error') && null;
      const err = ratingInput.closest('.rf-row').querySelector('.rf-error') ||
        Object.assign(document.createElement('span'), { className: 'rf-error', textContent: 'Selecione uma nota antes de enviar.' });
      ratingInput.closest('.rf-row').appendChild(err);
      document.getElementById('star-selector').style.outline = '2px solid #c00';
    } else {
      document.getElementById('star-selector').style.outline = '';
    }
  });

  // Word counter
  const body = document.getElementById('review-body');
  const counter = document.getElementById('word-count');
  if (body) {
    function countWords(txt) {
      return txt.trim() === '' ? 0 : txt.trim().split(/\s+/).length;
    }
    body.addEventListener('input', function () {
      const wc = countWords(this.value);
      counter.textContent = wc;
      counter.style.color = wc > 100 ? '#c00' : '#888';
    });
    counter.textContent = countWords(body.value);
  }

  // Image previews (max 3)
  const imgInput = document.getElementById('img-input');
  const imgPreviews = document.getElementById('img-previews');
  if (imgInput) {
    imgInput.addEventListener('change', function () {
      imgPreviews.innerHTML = '';
      const files = Array.from(this.files).slice(0, 3);
      files.forEach(f => {
        const url = URL.createObjectURL(f);
        const wrap = document.createElement('div');
        wrap.className = 'rf-preview-item';
        wrap.innerHTML = '<img src="' + url + '" alt="preview">';
        imgPreviews.appendChild(wrap);
      });
    });
  }

  // Video preview
  const vidInput = document.getElementById('video-input');
  const vidPreview = document.getElementById('video-preview');
  if (vidInput) {
    vidInput.addEventListener('change', function () {
      vidPreview.innerHTML = '';
      if (this.files[0]) {
        const url = URL.createObjectURL(this.files[0]);
        vidPreview.innerHTML = '<video class="rf-video-preview" src="' + url + '" controls></video>';
      }
    });
  }
})();

// ── Edit Review Modal ─────────────────────────────────────
(function () {
  const editStars = document.querySelectorAll('.edit-star');
  const editRatingInput = document.getElementById('edit-rating-input');
  let editCurrent = 0;

  function paintEditStars(val) {
    editStars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.val) <= val));
  }

  editStars.forEach(s => {
    s.addEventListener('mouseenter', () => paintEditStars(parseInt(s.dataset.val)));
    s.addEventListener('mouseleave', () => paintEditStars(editCurrent));
    s.addEventListener('click', () => {
      editCurrent = parseInt(s.dataset.val);
      editRatingInput.value = editCurrent;
      paintEditStars(editCurrent);
    });
  });

  window.openEditReview = function (id, name, rating, body) {
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-body').value = body || '';
    editCurrent = rating;
    editRatingInput.value = rating;
    paintEditStars(rating);
    document.getElementById('rev-edit-form').action = '/avaliacao/' + id;
    document.getElementById('rev-edit-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };

  window.closeEditReview = function () {
    document.getElementById('rev-edit-overlay').style.display = 'none';
    document.body.style.overflow = '';
  };

  document.getElementById('rev-edit-form').addEventListener('submit', function (e) {
    if (parseInt(editRatingInput.value) < 1) {
      e.preventDefault();
      document.getElementById('edit-star-selector').style.outline = '2px solid #c00';
    } else {
      document.getElementById('edit-star-selector').style.outline = '';
    }
  });
})();
</script>
@endpush
