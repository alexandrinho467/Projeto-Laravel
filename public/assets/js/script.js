// =========================================
// DADOS DOS PRODUTOS
// =========================================
const products = [
  {
    id: 1,
    brand: "Nike",
    name: "Calm Mule",
    price: 999,
    img1: "assets/images/tenis%201.png",
    img2: "assets/images/Tenis%2011.png",
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: false },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: false }, { n: "44", ok: true }
    ]
  },
  {
    id: 2,
    brand: "Nike",
    name: "Air Max 1/97 Sean Wotherspoon",
    price: 4299,
    img1: "assets/images/tenis%202.png",
    img2: "assets/images/tenis%2022.png",
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: false }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: false }
    ]
  },
  {
    id: 3,
    brand: "Nike",
    name: "Air Force 1 Low '07 Triple Black",
    price: 899,
    img1: "assets/images/tenis%203.png",
    img2: "assets/images/tenis%2033.png",
    sizes: [
      { n: "38", ok: false }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: false }, { n: "43", ok: true  }, { n: "44", ok: true }
    ]
  },
  {
    id: 4,
    brand: "Balmain",
    name: "Unicorn Low-Top",
    price: 4199,
    img1: "assets/images/tenis%204.png",
    img2: "assets/images/tenis%2044.png",
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: false }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: true }
    ]
  },
  {
    id: 5,
    brand: "Nike",
    name: "NOCTA Hot Step 2",
    price: 1899,
    img1: "assets/images/tenis%205.png",
    img2: "assets/images/tenis%2055.png",
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: false },
      { n: "41", ok: true  }, { n: "42", ok: false }, { n: "43", ok: true  }, { n: "44", ok: true }
    ]
  },
  {
    id: 6,
    brand: "Jordan",
    name: "Air Jordan 5 Retro OG Black Metallic",
    price: 3199,
    img1: "assets/images/tenis%206.png",
    img2: "assets/images/tenis%2066.png",
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: false }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: false }, { n: "44", ok: true }
    ]
  },
  {
    id: 7,
    brand: "Salomon",
    name: "XT-4 OG Aurora Borealis",
    price: 1599,
    img1: "assets/images/tenis%207.png",
    img2: "assets/images/tenis%2077.png",
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: false }, { n: "43", ok: true  }, { n: "44", ok: false }
    ]
  },
  {
    id: 8,
    brand: "Nike",
    name: "Air Max 95 OG Neon",
    price: 2299,
    img1: "assets/images/tenis%208.png",
    img2: "assets/images/tenis%208.png",
    sizes: [
      { n: "38", ok: false }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: true }
    ]
  },
  {
    id: 9,
    brand: "Converse",
    name: "Chuck 70 Hi Distressed",
    price: 1099,
    img1: "assets/images/tenis%209.png",
    img2: "assets/images/tenis%2099.png",
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: false },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: true }
    ]
  }
];

const miniBanners = [
  {
    img: "https://images.unsplash.com/photo-1556906781-9a412961a28c?w=1600&auto=format&fit=crop",
    text: "Lançamentos Masculinos"
  },
  {
    img: "https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=1600&auto=format&fit=crop",
    text: "Coleção Feminina"
  }
];

// =========================================
// CARRINHO (localStorage compartilhado com produto.html)
// =========================================
function getCart() {
  try { return JSON.parse(localStorage.getItem('ad_cart') || '[]'); }
  catch (e) { return []; }
}

function saveCart(c) {
  localStorage.setItem('ad_cart', JSON.stringify(c));
}

let selectedSizes = {};

// =========================================
// UTILITÁRIOS
// =========================================
function formatPrice(value) {
  return 'AED ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// =========================================
// RENDERIZAÇÃO DOS PRODUTOS
// =========================================
function renderProducts() {
  const grid = document.getElementById('products-grid');
  grid.innerHTML = '';
  let bannerIndex = 0;

  products.forEach(function(p, i) {
    if (i > 0 && i % 8 === 0 && bannerIndex < miniBanners.length) {
      const b = miniBanners[bannerIndex++];
      const banner = document.createElement('div');
      banner.className = 'mini-banner';
      banner.innerHTML =
        '<img src="' + b.img + '" alt="' + b.text + '">' +
        '<div class="mini-banner-overlay">' +
          '<h2>' + b.text + '</h2>' +
          '<button class="banner-btn">Ver Coleção</button>' +
        '</div>';
      grid.appendChild(banner);
    }

    const sizesHTML = p.sizes.map(function(s) {
      const cls = s.ok ? 'size-box' : 'size-box esgotado';
      const onclick = s.ok
        ? 'onclick="selectSize(' + p.id + ', \'' + s.n + '\', this)"'
        : '';
      return '<div class="' + cls + '" ' + onclick + '>' + s.n + '</div>';
    }).join('');

    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML =
      '<a class="card-img-link" href="produto.html?id=' + p.id + '">' +
        '<div class="product-image-wrap">' +
          '<img class="img-main" src="' + p.img1 + '" alt="' + p.name + '" loading="lazy">' +
          '<img class="img-hover" src="' + p.img2 + '" alt="' + p.name + ' ângulo 2" loading="lazy">' +
        '</div>' +
      '</a>' +
      '<div class="product-info">' +
        '<a class="card-name-link" href="produto.html?id=' + p.id + '">' +
          '<div class="product-brand">' + p.brand + '</div>' +
          '<div class="product-name">' + p.name + '</div>' +
        '</a>' +
        '<div class="product-price">' + formatPrice(p.price) + '</div>' +
        '<div class="sizes" id="sizes-' + p.id + '">' + sizesHTML + '</div>' +
        '<button class="add-to-cart-btn" onclick="addToCart(' + p.id + ')">Adicionar ao Carrinho</button>' +
      '</div>';

    grid.appendChild(card);
  });
}

// =========================================
// SELEÇÃO DE TAMANHO
// =========================================
function selectSize(productId, size, el) {
  selectedSizes[productId] = size;
  const container = document.getElementById('sizes-' + productId);
  container.querySelectorAll('.size-box:not(.esgotado)').forEach(function(b) {
    b.classList.remove('selected');
  });
  el.classList.add('selected');
}

// =========================================
// ADICIONAR AO CARRINHO
// =========================================
function addToCart(productId) {
  const size = selectedSizes[productId];
  if (!size) {
    showToast('Selecione um tamanho primeiro');
    return;
  }

  const p = products.find(function(x) { return x.id === productId; });
  const cart = getCart();
  const existing = cart.find(function(x) { return x.id === productId && x.size === size; });

  if (existing) {
    existing.qty++;
  } else {
    cart.push({ id: p.id, brand: p.brand, name: p.name, price: p.price, img: p.img1, size: size, qty: 1 });
  }

  saveCart(cart);
  updateCartUI();
  showToast('Produto adicionado ao carrinho!');
}

// =========================================
// REMOVER DO CARRINHO
// =========================================
function removeFromCart(index) {
  const cart = getCart();
  cart.splice(index, 1);
  saveCart(cart);
  updateCartUI();
}

// =========================================
// ATUALIZAR UI DO CARRINHO
// =========================================
function updateCartUI() {
  const cart = getCart();
  const totalValue = cart.reduce(function(sum, x) { return sum + x.price * x.qty; }, 0);
  const totalCount = cart.reduce(function(sum, x) { return sum + x.qty; }, 0);

  document.getElementById('cart-badge').textContent = totalCount;

  const floatingCart = document.getElementById('floating-cart');
  if (totalCount > 0) {
    floatingCart.classList.add('visible');
  } else {
    floatingCart.classList.remove('visible');
  }

  const itemsEl  = document.getElementById('cart-items');
  const footerEl = document.getElementById('cart-footer');
  const totalEl  = document.getElementById('cart-total-value');

  if (cart.length === 0) {
    itemsEl.innerHTML = '<div class="cart-empty">Seu carrinho está vazio.</div>';
    footerEl.style.display = 'none';
  } else {
    footerEl.style.display = 'block';
    totalEl.textContent = formatPrice(totalValue);
    itemsEl.innerHTML = cart.map(function(item, idx) {
      return (
        '<div class="cart-item">' +
          '<img src="' + item.img + '" alt="' + item.name + '">' +
          '<div class="cart-item-info">' +
            '<div class="ci-brand">' + item.brand + '</div>' +
            '<div class="ci-name">' + item.name + '</div>' +
            '<div class="ci-size">Tam: ' + item.size + (item.qty > 1 ? ' &times; ' + item.qty : '') + '</div>' +
            '<div class="ci-price">' + formatPrice(item.price * item.qty) + '</div>' +
          '</div>' +
          '<button class="remove-item" data-idx="' + idx + '">&#x2715;</button>' +
        '</div>'
      );
    }).join('');

    itemsEl.querySelectorAll('.remove-item').forEach(function(btn) {
      btn.addEventListener('click', function() {
        removeFromCart(parseInt(btn.dataset.idx));
      });
    });
  }
}

// =========================================
// ABRIR / FECHAR PAINEL DO CARRINHO
// =========================================
let cartOpen = false;

function toggleCart() {
  cartOpen = !cartOpen;
  document.getElementById('cart-panel').classList.toggle('open', cartOpen);
  document.getElementById('cart-overlay').classList.toggle('open', cartOpen);
  document.body.style.overflow = cartOpen ? 'hidden' : '';
}

// =========================================
// TOAST
// =========================================
var toastTimer;
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2600);
}

// =========================================
// ESPAÇADOR DINÂMICO DO HEADER
// =========================================
function adjustHeaderSpacing() {
  const h = document.getElementById('site-header').offsetHeight;
  document.getElementById('header-spacer').style.height = h + 'px';
}

// =========================================
// INICIALIZAÇÃO
// =========================================
document.addEventListener('DOMContentLoaded', function() {
  renderProducts();
  adjustHeaderSpacing();

  document.getElementById('cart-float-btn').addEventListener('click', toggleCart);
  document.getElementById('close-cart').addEventListener('click', toggleCart);
  document.getElementById('cart-overlay').addEventListener('click', toggleCart);

  window.addEventListener('resize', adjustHeaderSpacing);
});
