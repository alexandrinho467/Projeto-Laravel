// =========================================
// DADOS COMPLETOS DOS PRODUTOS
// =========================================
const products = [
  {
    id: 1,
    brand: "Nike",
    name: "Calm Mule",
    badge: "Campeã de Vendas",
    oldPrice: 1299,
    price: 999,
    ref: "NK-CM-001-2025",
    colors: [
      { name: "Anthracite / Orange", img1: "assets/images/tenis%201.png", img2: "assets/images/Tenis%2011.png" }
    ],
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: false },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: false }, { n: "44", ok: true }
    ],
    description: "A inovadora Nike Calm Mule combina conforto máximo com design arrojado. Desenvolvida para uso casual e lifestyle, a sola altamente amortecida em espuma proporciona uma sensação de leveza incomparável. O solado com pods de tração laranja oferece aderência em diversas superfícies, enquanto o design slip-on facilita o uso no dia a dia sem abrir mão do estilo.",
    tech: "Solado em espuma de alta densidade com pods de tração laranja. Upper em malha sintética respirável. Design mule sem cadarços. Peso aproximado: 280g (numeração 42).",
    features: [
      "Solado ultra-macio com amortecimento superior",
      "Design mule slip-on de fácil entrada e saída",
      "Pods de tração laranja no solado para aderência",
      "Upper em malha sintética respirável",
      "Ideal para uso diário e lifestyle urbano"
    ]
  },
  {
    id: 2,
    brand: "Nike",
    name: "Air Max 1/97 Sean Wotherspoon",
    badge: "Edição Limitada",
    oldPrice: 5499,
    price: 4299,
    ref: "NK-AM197-SW-2025",
    colors: [
      { name: "Multi-Color / Volt", img1: "assets/images/tenis%202.png", img2: "assets/images/tenis%2022.png" }
    ],
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: false }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: false }
    ],
    description: "A colaboração mais aguardada entre Nike e Sean Wotherspoon une as silhuetas icônicas Air Max 1 e Air Max 97 em um único modelo exclusivo. O upper em veludo cortado à mão e couro sintético apresenta uma paleta de cores vibrante inspirada nos anos 80, tornando-o um dos modelos mais desejados e colecionados de todos os tempos.",
    tech: "Câmaras Air Max 1 (forefoot) e Air Max 97 (heel) visíveis. Upper em veludo artesanal e couro sintético multicamadas. Solado em borracha translúcida. Numeração BR 35,5 a 45.",
    features: [
      "Colaboração exclusiva Nike x Sean Wotherspoon",
      "Câmaras Air Max 1 e Air Max 97 combinadas",
      "Upper em veludo cortado artesanalmente",
      "Paleta de cores vibrante dos anos 80",
      "Edição limitada de alto valor colecionável"
    ]
  },
  {
    id: 3,
    brand: "Nike",
    name: "Air Force 1 Low '07 Triple Black",
    badge: "Preço Exclusivo Site",
    oldPrice: 1199,
    price: 899,
    ref: "NK-AF1-TB-2025",
    colors: [
      { name: "Triple Black", img1: "assets/images/tenis%203.png", img2: "assets/images/tenis%2033.png" }
    ],
    sizes: [
      { n: "38", ok: false }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: false }, { n: "43", ok: true  }, { n: "44", ok: true }
    ],
    description: "O clássico absoluto do streetwear em sua versão mais elegante e minimalista: o Triple Black. A silhueta atemporal da Air Force 1 Low ganha nova dimensão com o couro plena flor premium todo em preto, criando um visual sofisticado e extremamente versátil que combina com qualquer look, em qualquer ocasião.",
    tech: "Upper em couro plena flor premium todo preto. Câmara Air na entressola para amortecimento. Solado em borracha durável com frisos de tração. Peso aproximado: 250g (numeração 42).",
    features: [
      "Couro plena flor premium all-black",
      "Câmara Air para amortecimento superior",
      "Visual atemporal e versátil",
      "Ícone absoluto do streetwear mundial",
      "Combina com qualquer estilo e ocasião"
    ]
  },
  {
    id: 4,
    brand: "Balmain",
    name: "Unicorn Low-Top",
    badge: "Luxo Importado",
    oldPrice: 5299,
    price: 4199,
    ref: "BLM-UC-LT-2025",
    colors: [
      { name: "All Black", img1: "assets/images/tenis%204.png", img2: "assets/images/tenis%2044.png" }
    ],
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: false }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: true }
    ],
    description: "O Unicorn Low-Top é a expressão máxima do luxo parisiense em forma de tênis. Com design futurista e construção em camadas de couro e material sintético de alta qualidade, cada detalhe reflete o DNA da maison Balmain. A fivela metálica exclusiva e os bordados característicos tornam este modelo verdadeiramente único.",
    tech: "Upper em couro e material sintético multicamadas. Solado em borracha de alta densidade. Fivela metálica exclusiva Balmain. Numeração europeia. Fabricação artesanal na Itália.",
    features: [
      "Design futurista assinado pela maison Balmain",
      "Fivela metálica exclusiva da grife",
      "Couro e materiais premium de alta qualidade",
      "Solado chunky de alta densidade",
      "Símbolo máximo de luxo e status"
    ]
  },
  {
    id: 5,
    brand: "Nike",
    name: "NOCTA Hot Step 2",
    badge: "Collab Exclusiva",
    oldPrice: 2499,
    price: 1899,
    ref: "NK-NOCTA-HS2-2025",
    colors: [
      { name: "Fire Red / Black", img1: "assets/images/tenis%205.png", img2: "assets/images/tenis%2055.png" }
    ],
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: false },
      { n: "41", ok: true  }, { n: "42", ok: false }, { n: "43", ok: true  }, { n: "44", ok: true }
    ],
    description: "Desenvolvido em colaboração com Drake e seu label NOCTA, o Hot Step 2 redefine o conceito de tênis de lifestyle de luxo. O design aerodinâmico em vermelho Ferrari com detalhes em preto incorpora elementos da cultura do basquete com estética contemporânea e futurista que domina os looks das maiores celebridades.",
    tech: "Upper em malha sintética de alta resistência e couro premium. Unidade Air Strobel integrada para amortecimento responsivo. Solado em borracha translúcida com frisos. Peso: 320g (numeração 42).",
    features: [
      "Colaboração exclusiva Nike x Drake / NOCTA",
      "Colorway Fire Red inconfundível",
      "Unidade Air Strobel para amortecimento",
      "Design aerodinâmico e futurista",
      "Símbolo da cultura pop e streetwear atual"
    ]
  },
  {
    id: 6,
    brand: "Jordan",
    name: "Air Jordan 5 Retro OG Black Metallic",
    badge: "Retro OG",
    oldPrice: 3999,
    price: 3199,
    ref: "JD-AJ5-OG-BM-2025",
    colors: [
      { name: "Black / Metallic Silver", img1: "assets/images/tenis%206.png", img2: "assets/images/tenis%2066.png" }
    ],
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: false }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: false }, { n: "44", ok: true }
    ],
    description: "O lendário Air Jordan 5 retorna em sua versão OG original, fiel ao modelo de 1990 criado por Tinker Hatfield. A combinação de camurça preta com detalhes metálicos prateados e a rede na lateral homenageia o legado de Michael Jordan nas quadras. Cada par acompanha card de autenticidade Jordan Brand.",
    tech: "Upper em camurça de alta qualidade com painéis em rede. Câmara Air visível no calcanhar. Solado em borracha translúcida com frisos. Forro interno em tecido premium.",
    features: [
      "Fiel ao modelo OG original de 1990",
      "Câmara Air visível no calcanhar",
      "Upper em camurça premium preta",
      "Rede lateral icônica do Jordan 5",
      "Acompanha card de autenticidade Jordan Brand"
    ]
  },
  {
    id: 7,
    brand: "Salomon",
    name: "XT-4 OG Aurora Borealis",
    badge: "Collab Especial",
    oldPrice: 1999,
    price: 1599,
    ref: "SLM-XT4-AB-2025",
    colors: [
      { name: "Aurora Borealis", img1: "assets/images/tenis%207.png", img2: "assets/images/tenis%2077.png" }
    ],
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: false }, { n: "43", ok: true  }, { n: "44", ok: false }
    ],
    description: "O XT-4 OG Aurora Borealis é uma ode à natureza em forma de tênis. O colorway iridescente imita as cores da aurora boreal, com painéis em tons de verde, azul e prateado que mudam conforme a incidência de luz. A tecnologia de trail running de alta performance da Salomon é aplicada em uma silhueta urbana e fashion.",
    tech: "Upper com chassis Sensifit de ajuste anatômico. Solado Contagrip TA multitracional para trilhas e asfalto. Amortecimento EnergyCell de alta responsividade. Sistema de cadarço Quick Lace patenteado.",
    features: [
      "Colorway iridescente Aurora Borealis exclusivo",
      "Tecnologia Contagrip para máxima tração",
      "Sistema Quick Lace para ajuste rápido e preciso",
      "Upper Sensifit com encaixe anatômico",
      "Favorito global da cena fashion e streetwear"
    ]
  },
  {
    id: 8,
    brand: "Nike",
    name: "Air Max 95 OG Neon",
    badge: "Retro OG",
    oldPrice: 2899,
    price: 2299,
    ref: "NK-AM95-OG-NN-2025",
    colors: [
      { name: "Neon Yellow / Anthracite", img1: "assets/images/tenis%208.png", img2: "assets/images/tenis%208.png" }
    ],
    sizes: [
      { n: "38", ok: false }, { n: "39", ok: true  }, { n: "40", ok: true  },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: true }
    ],
    description: "O Air Max 95 OG 'Neon' é um dos tênis mais icônicos já criados pela Nike. Inspirado na anatomia humana pelo designer Sergio Lozano em 1995, o gradiente cinza-preto com detalhes em amarelo neon é o colorway original e continua sendo um dos modelos mais desejados por colecionadores e entusiastas do sneaker culture.",
    tech: "Câmaras Air duplas: forefoot e heel para amortecimento completo em toda a pisada. Upper em camadas de malha e couro sintético. Solado Max Waffle em borracha. Peso: 290g (numeração 42).",
    features: [
      "Colorway OG original fiel ao lançamento de 1995",
      "Câmaras Air duplas: forefoot e heel",
      "Design inspirado na anatomia humana",
      "Criado pelo lendário designer Sergio Lozano",
      "Um dos modelos mais cobiçados da história da Nike"
    ]
  },
  {
    id: 9,
    brand: "Converse",
    name: "Chuck 70 Hi Distressed",
    badge: "Edição Especial",
    oldPrice: 1399,
    price: 1099,
    ref: "CVS-CK70-DST-2025",
    colors: [
      { name: "Dark Olive / Red", img1: "assets/images/tenis%209.png", img2: "assets/images/tenis%2099.png" }
    ],
    sizes: [
      { n: "38", ok: true  }, { n: "39", ok: true  }, { n: "40", ok: false },
      { n: "41", ok: true  }, { n: "42", ok: true  }, { n: "43", ok: true  }, { n: "44", ok: true }
    ],
    description: "Uma releitura artística e provocadora do ícone Chuck Taylor. O acabamento distressed feito à mão em lona marrom com detalhes em vermelho cria um visual raww e autêntico que desafia as convenções do streetwear contemporâneo. Cada par é genuinamente único devido ao processo de manufatura artesanal.",
    tech: "Upper em lona de algodão pesado com tratamento distressed aplicado manualmente. Palmilha OrthoLite para conforto prolongado. Solado em borracha vulcanizada. Produção artesanal — cada par é único.",
    features: [
      "Acabamento distressed único aplicado à mão",
      "Lona de algodão premium de alta gramatura",
      "Palmilha OrthoLite para conforto prolongado",
      "Cada par é genuinamente único",
      "Para quem busca autenticidade e originalidade"
    ]
  }
];

// =========================================
// ESTADO DA PÁGINA
// =========================================
var currentProduct = null;
var selectedSize = null;
var selectedColorIndex = 0;
var isFavorited = false;

// =========================================
// CARRINHO (localStorage compartilhado)
// =========================================
function getCart() {
  try { return JSON.parse(localStorage.getItem('ad_cart') || '[]'); }
  catch (e) { return []; }
}

function saveCart(cart) {
  localStorage.setItem('ad_cart', JSON.stringify(cart));
}

function formatPrice(value) {
  return 'AED ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// =========================================
// INICIALIZAÇÃO
// =========================================
document.addEventListener('DOMContentLoaded', function () {
  var params = new URLSearchParams(window.location.search);
  var id = parseInt(params.get('id'));
  currentProduct = products.find(function (p) { return p.id === id; }) || products[0];

  renderProduct();
  renderRelated();
  updateCartUI();
  initEvents();
  adjustHeaderSpacing();
  window.addEventListener('resize', adjustHeaderSpacing);
});

// =========================================
// RENDERIZAR PRODUTO
// =========================================
function renderProduct() {
  var p = currentProduct;

  document.title = p.name + ' | Dias Sneakers';
  document.getElementById('bc-brand').textContent = p.brand;
  document.getElementById('bc-product').textContent = p.name;

  // Badge
  var badgeEl = document.getElementById('product-badge');
  if (p.badge) {
    badgeEl.textContent = p.badge;
    badgeEl.style.display = 'inline-block';
  } else {
    badgeEl.style.display = 'none';
  }

  document.getElementById('product-brand-label').textContent = p.brand;
  document.getElementById('product-title').textContent = p.name;

  // Preços
  document.getElementById('old-price').innerHTML = 'De: <s>' + formatPrice(p.oldPrice) + '</s>';
  document.getElementById('current-price').textContent = formatPrice(p.price);
  var disc = Math.round((1 - p.price / p.oldPrice) * 100);
  document.getElementById('discount-tag').textContent = '-' + disc + '%';

  var parcela = p.price / 12;
  document.getElementById('installment').innerHTML =
    'ou <strong>12x</strong> de <strong>' + formatPrice(parcela) + '</strong> sem juros no cartão';

  // Descrição
  document.getElementById('product-description').textContent = p.description;
  document.getElementById('product-tech').textContent = p.tech;
  document.getElementById('product-features').innerHTML =
    p.features.map(function (f) { return '<li>' + f + '</li>'; }).join('');
  document.getElementById('product-ref').textContent = p.ref;

  renderGallery(0);
  renderColors();
  renderSizes();
  initZoom();
}

// =========================================
// GALERIA
// =========================================
function renderGallery(colorIndex) {
  var color = currentProduct.colors[colorIndex];
  var images = (color.img1 === color.img2) ? [color.img1] : [color.img1, color.img2];

  var strip = document.getElementById('thumb-strip');
  strip.innerHTML = images.map(function (img, i) {
    return '<div class="thumb-item ' + (i === 0 ? 'active' : '') + '" data-src="' + img + '">' +
      '<img src="' + img + '" alt="Vista ' + (i + 1) + '">' +
      '</div>';
  }).join('');

  strip.querySelectorAll('.thumb-item').forEach(function (el) {
    el.addEventListener('click', function () {
      strip.querySelectorAll('.thumb-item').forEach(function (t) { t.classList.remove('active'); });
      el.classList.add('active');
      document.getElementById('main-img').src = el.dataset.src;
    });
  });

  document.getElementById('main-img').src = images[0];
}

// =========================================
// ZOOM NA IMAGEM PRINCIPAL
// =========================================
function initZoom() {
  var container = document.getElementById('main-img-container');
  var img = document.getElementById('main-img');

  container.addEventListener('mouseenter', function () {
    img.style.transition = 'transform 0.3s ease';
    img.style.transform = 'scale(2.4)';
  });

  container.addEventListener('mousemove', function (e) {
    var rect = container.getBoundingClientRect();
    var x = ((e.clientX - rect.left) / rect.width * 100).toFixed(1) + '%';
    var y = ((e.clientY - rect.top) / rect.height * 100).toFixed(1) + '%';
    img.style.transition = 'none';
    img.style.transformOrigin = x + ' ' + y;
  });

  container.addEventListener('mouseleave', function () {
    img.style.transition = 'transform 0.3s ease';
    img.style.transform = 'scale(1)';
    img.style.transformOrigin = '50% 50%';
  });
}

// =========================================
// SELETOR DE COR
// =========================================
function renderColors() {
  var p = currentProduct;
  var swatchesEl = document.getElementById('color-swatches');

  swatchesEl.innerHTML = p.colors.map(function (c, i) {
    return '<div class="color-swatch ' + (i === 0 ? 'active' : '') + '" data-index="' + i + '" title="' + c.name + '">' +
      '<img src="' + c.img1 + '" alt="' + c.name + '">' +
      '</div>';
  }).join('');

  document.getElementById('color-name').textContent = p.colors[0].name;

  swatchesEl.querySelectorAll('.color-swatch').forEach(function (el) {
    el.addEventListener('click', function () {
      var index = parseInt(el.dataset.index);
      selectedColorIndex = index;
      document.getElementById('color-name').textContent = p.colors[index].name;
      swatchesEl.querySelectorAll('.color-swatch').forEach(function (s) { s.classList.remove('active'); });
      el.classList.add('active');
      renderGallery(index);
    });
  });
}

// =========================================
// SELETOR DE TAMANHO
// =========================================
function renderSizes() {
  var sizesEl = document.getElementById('sizes-panel');
  sizesEl.innerHTML = currentProduct.sizes.map(function (s) {
    var cls = 'size-box' + (s.ok ? '' : ' esgotado');
    return '<div class="' + cls + '" data-size="' + s.n + '" data-ok="' + s.ok + '">' + s.n + '</div>';
  }).join('');

  sizesEl.querySelectorAll('.size-box:not(.esgotado)').forEach(function (el) {
    el.addEventListener('click', function () {
      sizesEl.querySelectorAll('.size-box').forEach(function (b) { b.classList.remove('selected'); });
      el.classList.add('selected');
      selectedSize = el.dataset.size;
      document.getElementById('size-warning').style.display = 'none';
    });
  });

  selectedSize = null;
}

// =========================================
// ADICIONAR AO CARRINHO
// =========================================
function addToCart() {
  if (!selectedSize) {
    document.getElementById('size-warning').style.display = 'block';
    return false;
  }

  var p = currentProduct;
  var color = p.colors[selectedColorIndex];
  var cart = getCart();
  var existing = cart.find(function (x) {
    return x.id === p.id && x.size === selectedSize;
  });

  if (existing) {
    existing.qty++;
  } else {
    cart.push({ id: p.id, brand: p.brand, name: p.name, price: p.price, img: color.img1, size: selectedSize, qty: 1 });
  }

  saveCart(cart);
  updateCartUI();
  showToast('Produto adicionado ao carrinho!');
  return true;
}

// =========================================
// REMOVER DO CARRINHO
// =========================================
function removeFromCart(index) {
  var cart = getCart();
  cart.splice(index, 1);
  saveCart(cart);
  updateCartUI();
}

// =========================================
// ATUALIZAR UI DO CARRINHO
// =========================================
function updateCartUI() {
  var cart = getCart();
  var totalValue = cart.reduce(function (s, x) { return s + x.price * x.qty; }, 0);
  var totalCount = cart.reduce(function (s, x) { return s + x.qty; }, 0);

  document.getElementById('cart-badge').textContent = totalCount;

  var floatingCart = document.getElementById('floating-cart');
  if (totalCount > 0) { floatingCart.classList.add('visible'); }
  else { floatingCart.classList.remove('visible'); }

  var itemsEl = document.getElementById('cart-items');
  var footerEl = document.getElementById('cart-footer');

  if (cart.length === 0) {
    itemsEl.innerHTML = '<div class="cart-empty">Seu carrinho está vazio.</div>';
    footerEl.style.display = 'none';
  } else {
    footerEl.style.display = 'block';
    document.getElementById('cart-total-value').textContent = formatPrice(totalValue);
    itemsEl.innerHTML = cart.map(function (item, idx) {
      return '<div class="cart-item">' +
        '<img src="' + item.img + '" alt="' + item.name + '">' +
        '<div class="cart-item-info">' +
          '<div class="ci-brand">' + item.brand + '</div>' +
          '<div class="ci-name">' + item.name + '</div>' +
          '<div class="ci-size">Tam: ' + item.size + (item.qty > 1 ? ' &times; ' + item.qty : '') + '</div>' +
          '<div class="ci-price">' + formatPrice(item.price * item.qty) + '</div>' +
        '</div>' +
        '<button class="remove-item" data-index="' + idx + '">&#x2715;</button>' +
      '</div>';
    }).join('');

    itemsEl.querySelectorAll('.remove-item').forEach(function (btn) {
      btn.addEventListener('click', function () {
        removeFromCart(parseInt(btn.dataset.index));
      });
    });
  }
}

// =========================================
// PAINEL DO CARRINHO
// =========================================
var cartOpen = false;

function toggleCart() {
  cartOpen = !cartOpen;
  document.getElementById('cart-panel').classList.toggle('open', cartOpen);
  document.getElementById('cart-overlay').classList.toggle('open', cartOpen);
  document.body.style.overflow = cartOpen ? 'hidden' : '';
}

// =========================================
// CALCULADORA DE FRETE
// =========================================
function calcShipping() {
  var cep = document.getElementById('cep-input').value.replace(/\D/g, '');
  if (cep.length < 8) { showToast('Digite um CEP válido com 8 dígitos'); return; }

  var optionsEl = document.getElementById('shipping-options');
  optionsEl.innerHTML = '<div class="shipping-loading">Calculando frete...</div>';

  setTimeout(function () {
    var isFree = currentProduct.price >= 1500;
    optionsEl.innerHTML =
      '<div class="shipping-option">' +
        '<div class="ship-info"><span class="ship-name">PAC</span><span class="ship-days">Entrega em 8 dias úteis</span></div>' +
        '<span class="ship-price ' + (isFree ? 'gratis' : '') + '">' + (isFree ? 'Grátis' : formatPrice(19.90)) + '</span>' +
      '</div>' +
      '<div class="shipping-option">' +
        '<div class="ship-info"><span class="ship-name">SEDEX</span><span class="ship-days">Entrega em 3 dias úteis</span></div>' +
        '<span class="ship-price">' + formatPrice(34.90) + '</span>' +
      '</div>' +
      '<div class="shipping-option">' +
        '<div class="ship-info"><span class="ship-name">SEDEX 10</span><span class="ship-days">Entrega amanhã até 10h</span></div>' +
        '<span class="ship-price">' + formatPrice(59.90) + '</span>' +
      '</div>';
  }, 900);
}

// =========================================
// PRODUTOS RELACIONADOS
// =========================================
function renderRelated() {
  var p = currentProduct;
  var related = products.filter(function (x) { return x.id !== p.id; }).slice(0, 4);
  var grid = document.getElementById('related-grid');

  grid.innerHTML = related.map(function (r) {
    var sizesHTML = r.sizes.map(function (s) {
      return '<div class="size-box' + (s.ok ? '' : ' esgotado') + '">' + s.n + '</div>';
    }).join('');

    return '<div class="product-card" onclick="window.location.href=\'produto.html?id=' + r.id + '\'">' +
      '<div class="product-image-wrap">' +
        '<img class="img-main" src="' + r.colors[0].img1 + '" alt="' + r.name + '" loading="lazy">' +
        '<img class="img-hover" src="' + r.colors[0].img2 + '" alt="' + r.name + '" loading="lazy">' +
      '</div>' +
      '<div class="product-info">' +
        '<div class="product-brand">' + r.brand + '</div>' +
        '<div class="product-name">' + r.name + '</div>' +
        '<div class="product-price">' + formatPrice(r.price) + '</div>' +
        '<div class="sizes">' + sizesHTML + '</div>' +
        '<button class="add-to-cart-btn" onclick="event.stopPropagation()">Adicionar ao Carrinho</button>' +
      '</div>' +
    '</div>';
  }).join('');
}

// =========================================
// MODAL GUIA DE TAMANHOS
// =========================================
function openSizeGuide(e) {
  e.preventDefault();
  document.getElementById('modal-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeSizeGuide() {
  document.getElementById('modal-overlay').classList.remove('open');
  if (!cartOpen) document.body.style.overflow = '';
}

// =========================================
// TOAST
// =========================================
var toastTimer;
function showToast(msg) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(function () { t.classList.remove('show'); }, 2600);
}

// =========================================
// HEADER SPACER
// =========================================
function adjustHeaderSpacing() {
  var h = document.getElementById('site-header').offsetHeight;
  document.getElementById('header-spacer').style.height = h + 'px';
}

// =========================================
// EVENTOS
// =========================================
function initEvents() {
  // Carrinho
  document.getElementById('cart-float-btn').addEventListener('click', toggleCart);
  document.getElementById('close-cart').addEventListener('click', toggleCart);
  document.getElementById('cart-overlay').addEventListener('click', function (e) {
    if (e.target === this) toggleCart();
  });

  // Botão Comprar Agora
  document.getElementById('btn-buy-now').addEventListener('click', function () {
    var added = addToCart();
    if (added) showToast('Redirecionando para o checkout...');
  });

  // Botão Adicionar ao Carrinho
  document.getElementById('btn-add-cart').addEventListener('click', addToCart);

  // Botão Favoritar
  document.getElementById('btn-fav').addEventListener('click', function () {
    isFavorited = !isFavorited;
    var btn = document.getElementById('btn-fav');
    btn.classList.toggle('favorited', isFavorited);
    showToast(isFavorited ? 'Adicionado aos favoritos!' : 'Removido dos favoritos');
  });

  // CEP
  document.getElementById('cep-input').addEventListener('input', function () {
    var v = this.value.replace(/\D/g, '');
    if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5, 8);
    this.value = v;
  });
  document.getElementById('cep-input').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') calcShipping();
  });
  document.getElementById('cep-btn').addEventListener('click', calcShipping);

  // Guia de tamanhos
  document.getElementById('size-guide-link').addEventListener('click', openSizeGuide);
  document.getElementById('close-modal').addEventListener('click', closeSizeGuide);
  document.getElementById('modal-overlay').addEventListener('click', function (e) {
    if (e.target === this) closeSizeGuide();
  });
}
