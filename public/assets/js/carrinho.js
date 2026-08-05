// =========================================
// CUPONS VÁLIDOS
// =========================================
var COUPONS = {
  'DIAS10':       { type: 'percent', value: 10,  label: '10% de desconto aplicado!' },
  'DIAS20':       { type: 'percent', value: 20,  label: '20% de desconto aplicado!' },
  'FRETEGRATIS':  { type: 'shipping', value: 0,  label: 'Frete grátis aplicado!' }
};

// =========================================
// ESTADO
// =========================================
var appliedCoupon = null;
var selectedShipping = null; // { type, price, days, label }

// =========================================
// HELPERS
// =========================================
function getCart() {
  try { return JSON.parse(localStorage.getItem('ad_cart') || '[]'); }
  catch (e) { return []; }
}

function saveCart(c) { localStorage.setItem('ad_cart', JSON.stringify(c)); }

function getCheckout() {
  try { return JSON.parse(localStorage.getItem('ad_checkout') || '{}'); }
  catch (e) { return {}; }
}

function saveCheckout(data) {
  var prev = getCheckout();
  localStorage.setItem('ad_checkout', JSON.stringify(Object.assign(prev, data)));
}

function formatPrice(v) {
  return 'AED ' + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

var toastTimer;
function showToast(msg) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(function () { t.classList.remove('show'); }, 2600);
}

// =========================================
// RENDERIZAR ITENS
// =========================================
function renderCart() {
  var cart = getCart();
  var list = document.getElementById('cart-items-list');

  if (cart.length === 0) {
    list.innerHTML =
      '<div class="cart-empty-state">' +
        '<svg width="48" height="48" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24">' +
          '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>' +
          '<line x1="3" y1="6" x2="21" y2="6"/>' +
          '<path d="M16 10a4 4 0 01-8 0"/>' +
        '</svg>' +
        '<h2>Seu carrinho está vazio</h2>' +
        '<p>Adicione produtos para continuar comprando.</p>' +
        '<a href="index.html" class="btn-go-shopping">Continuar Comprando</a>' +
      '</div>';
    document.getElementById('checkout-sidebar').style.display = 'none';
    return;
  }

  list.innerHTML = cart.map(function (item, idx) {
    return '<div class="cart-page-item">' +
      '<img class="cart-page-item-img" src="' + item.img + '" alt="' + item.name + '">' +
      '<div class="cart-page-item-info">' +
        '<div class="cart-page-item-brand">' + item.brand + '</div>' +
        '<div class="cart-page-item-name">' + item.name + '</div>' +
        '<div class="cart-page-item-meta">Tamanho: ' + item.size + '</div>' +
        '<button class="cart-page-item-remove" data-idx="' + idx + '">Remover</button>' +
      '</div>' +
      '<div class="qty-control">' +
        '<button class="qty-btn" data-action="dec" data-idx="' + idx + '">−</button>' +
        '<div class="qty-value">' + item.qty + '</div>' +
        '<button class="qty-btn" data-action="inc" data-idx="' + idx + '">+</button>' +
      '</div>' +
      '<div class="cart-page-item-price">' + formatPrice(item.price * item.qty) + '</div>' +
    '</div>';
  }).join('');

  // Events
  list.querySelectorAll('.cart-page-item-remove').forEach(function (btn) {
    btn.addEventListener('click', function () {
      removeItem(parseInt(btn.dataset.idx));
    });
  });

  list.querySelectorAll('.qty-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      changeQty(parseInt(btn.dataset.idx), btn.dataset.action);
    });
  });

  updateSummary();
}

// =========================================
// REMOVER / QUANTIDADE
// =========================================
function removeItem(idx) {
  var cart = getCart();
  cart.splice(idx, 1);
  saveCart(cart);
  renderCart();
}

function changeQty(idx, action) {
  var cart = getCart();
  if (action === 'inc') {
    cart[idx].qty++;
  } else {
    if (cart[idx].qty > 1) {
      cart[idx].qty--;
    } else {
      cart.splice(idx, 1);
    }
  }
  saveCart(cart);
  renderCart();
}

// =========================================
// ATUALIZAR RESUMO
// =========================================
function updateSummary() {
  var cart = getCart();
  var subtotal = cart.reduce(function (s, x) { return s + x.price * x.qty; }, 0);

  var discount = 0;
  var shippingCost = null;

  if (appliedCoupon) {
    if (appliedCoupon.type === 'percent') {
      discount = subtotal * (appliedCoupon.value / 100);
    } else if (appliedCoupon.type === 'shipping') {
      shippingCost = 0;
    }
  }

  // Frete grátis acima de R$1500
  if (subtotal >= 1500 && shippingCost !== 0) {
    shippingCost = selectedShipping ? 0 : null;
  }

  if (selectedShipping) {
    if (appliedCoupon && appliedCoupon.type === 'shipping') {
      shippingCost = 0;
    } else if (subtotal >= 1500) {
      shippingCost = 0;
    } else {
      shippingCost = selectedShipping.price;
    }
  }

  var total = subtotal - discount + (shippingCost || 0);

  document.getElementById('sum-subtotal').textContent = formatPrice(subtotal);

  var discountRow = document.getElementById('sum-discount-row');
  if (discount > 0) {
    discountRow.style.display = 'flex';
    document.getElementById('sum-discount').textContent = '- ' + formatPrice(discount);
  } else {
    discountRow.style.display = 'none';
  }

  var shipEl = document.getElementById('sum-shipping');
  if (shippingCost === null) {
    shipEl.textContent = 'Calcule abaixo';
  } else if (shippingCost === 0) {
    shipEl.innerHTML = '<span style="color:#1a6b3a;font-weight:700">Grátis</span>';
  } else {
    shipEl.textContent = formatPrice(shippingCost);
  }

  document.getElementById('sum-total').textContent = formatPrice(total);
}

// =========================================
// CUPOM
// =========================================
function applyCoupon() {
  var code = document.getElementById('coupon-input').value.trim().toUpperCase();
  var fb = document.getElementById('coupon-feedback');
  fb.className = 'coupon-feedback';

  if (!code) {
    fb.textContent = 'Digite um código de cupom.';
    fb.classList.add('error');
    fb.style.display = 'block';
    return;
  }

  var coupon = COUPONS[code];
  if (!coupon) {
    fb.textContent = 'Cupom inválido ou expirado.';
    fb.classList.add('error');
    fb.style.display = 'block';
    appliedCoupon = null;
  } else {
    appliedCoupon = coupon;
    fb.textContent = '✓ ' + coupon.label;
    fb.classList.add('success');
    fb.style.display = 'block';
    saveCheckout({ coupon: { code: code, type: coupon.type, value: coupon.value } });
  }

  updateSummary();
}

// =========================================
// CALCULAR FRETE
// =========================================
function calcShipping() {
  var cep = document.getElementById('cep-input').value.replace(/\D/g, '');
  if (cep.length < 8) { showToast('Digite um CEP válido com 8 dígitos'); return; }

  var cart = getCart();
  var subtotal = cart.reduce(function (s, x) { return s + x.price * x.qty; }, 0);
  var isFree = subtotal >= 1500 || (appliedCoupon && appliedCoupon.type === 'shipping');

  var opts = document.getElementById('shipping-opts');
  opts.innerHTML = '<div style="font-size:0.78rem;color:#aaa;padding:6px 0;font-style:italic">Calculando...</div>';

  setTimeout(function () {
    var options = [
      { type: 'pac',      price: isFree ? 0 : 19.90, days: '8 dias úteis',         label: 'PAC'       },
      { type: 'sedex',    price: 34.90,                days: '3 dias úteis',         label: 'SEDEX'     },
      { type: 'sedex10',  price: 59.90,                days: 'amanhã até 10h',       label: 'SEDEX 10'  }
    ];

    opts.innerHTML = options.map(function (o, i) {
      var priceStr = (o.price === 0) ? '<span class="ship-opt-price gratis">Grátis</span>' :
                     '<span class="ship-opt-price">' + formatPrice(o.price) + '</span>';
      return '<label class="ship-option-label" data-idx="' + i + '">' +
        '<input type="radio" name="ship" value="' + o.type + '" ' + (i === 0 ? 'checked' : '') + '>' +
        '<div class="ship-opt-text">' +
          '<div class="ship-opt-name">' + o.label + '</div>' +
          '<div class="ship-opt-days">' + o.days + '</div>' +
        '</div>' +
        priceStr +
      '</label>';
    }).join('');

    // Select first by default
    selectedShipping = options[0];
    opts.querySelector('.ship-option-label').classList.add('selected');

    // Save
    saveCheckout({ cep: document.getElementById('cep-input').value, shipping: selectedShipping });
    updateSummary();

    // Radio events
    opts.querySelectorAll('input[type="radio"]').forEach(function (radio, i) {
      radio.addEventListener('change', function () {
        opts.querySelectorAll('.ship-option-label').forEach(function (l) { l.classList.remove('selected'); });
        radio.closest('.ship-option-label').classList.add('selected');
        selectedShipping = options[i];
        saveCheckout({ shipping: selectedShipping });
        updateSummary();
      });
    });
  }, 800);
}

// =========================================
// CONTINUAR
// =========================================
function continuar() {
  var cart = getCart();
  if (cart.length === 0) { showToast('Seu carrinho está vazio'); return; }
  if (!selectedShipping) { showToast('Calcule o frete antes de continuar'); return; }
  window.location.href = 'identificacao.html';
}

// =========================================
// CEP MASK
// =========================================
function maskCep(el) {
  el.addEventListener('input', function () {
    var v = this.value.replace(/\D/g, '');
    if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5, 8);
    this.value = v;
  });
  el.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') calcShipping();
  });
}

// =========================================
// INIT
// =========================================
document.addEventListener('DOMContentLoaded', function () {
  // Restaurar cupom salvo
  var saved = getCheckout();
  if (saved.coupon) {
    appliedCoupon = saved.coupon;
    var fb = document.getElementById('coupon-feedback');
    var couponDef = COUPONS[saved.coupon.code];
    if (couponDef) {
      fb.textContent = '✓ ' + couponDef.label;
      fb.className = 'coupon-feedback success';
      fb.style.display = 'block';
      document.getElementById('coupon-input').value = saved.coupon.code;
    }
  }

  renderCart();

  // Header spacer
  var h = document.getElementById('site-header').offsetHeight;
  document.getElementById('header-spacer').style.height = h + 'px';
  var sidebar = document.getElementById('checkout-sidebar');
  if (sidebar) sidebar.style.top = (h + 16) + 'px';

  window.addEventListener('resize', function () {
    var h2 = document.getElementById('site-header').offsetHeight;
    document.getElementById('header-spacer').style.height = h2 + 'px';
    if (sidebar) sidebar.style.top = (h2 + 16) + 'px';
  });

  // Cupom
  document.getElementById('coupon-btn').addEventListener('click', applyCoupon);
  document.getElementById('coupon-input').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') applyCoupon();
  });

  // CEP
  maskCep(document.getElementById('cep-input'));
  document.getElementById('cep-btn').addEventListener('click', calcShipping);

  // Continuar
  document.getElementById('btn-continue').addEventListener('click', continuar);
});
