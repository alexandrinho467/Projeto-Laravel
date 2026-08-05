// =========================================
// HELPERS
// =========================================
function getCart() {
  try { return JSON.parse(localStorage.getItem('ad_cart') || '[]'); }
  catch (e) { return []; }
}

function getCheckout() {
  try { return JSON.parse(localStorage.getItem('ad_checkout') || '{}'); }
  catch (e) { return {}; }
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
// CALCULAR TOTAL
// =========================================
function calcTotals() {
  var cart = getCart();
  var saved = getCheckout();
  var subtotal = cart.reduce(function (s, x) { return s + x.price * x.qty; }, 0);
  var discount = 0;
  if (saved.coupon && saved.coupon.type === 'percent') {
    discount = subtotal * (saved.coupon.value / 100);
  }
  var shippingCost = 0;
  if (saved.shipping) {
    if (saved.coupon && saved.coupon.type === 'shipping') {
      shippingCost = 0;
    } else if (subtotal >= 1500) {
      shippingCost = 0;
    } else {
      shippingCost = saved.shipping.price;
    }
  }
  var total = subtotal - discount + shippingCost;
  return { subtotal: subtotal, discount: discount, shippingCost: shippingCost, total: total };
}

// =========================================
// RENDERIZAR RESUMO
// =========================================
function renderOrderSummary() {
  var cart = getCart();
  var totals = calcTotals();
  var saved = getCheckout();

  var list = document.getElementById('pay-items-list');
  list.innerHTML = cart.map(function (item) {
    return '<div class="pay-summary-item">' +
      '<img src="' + item.img + '" alt="' + item.name + '">' +
      '<div class="pay-summary-item-info">' +
        '<div class="pay-item-name">' + item.brand + ' ' + item.name + '</div>' +
        '<div class="pay-item-meta">Tam. ' + item.size + ' &bull; Qtd. ' + item.qty + '</div>' +
      '</div>' +
      '<div class="pay-item-price">' + formatPrice(item.price * item.qty) + '</div>' +
    '</div>';
  }).join('');

  document.getElementById('pay-subtotal').textContent = formatPrice(totals.subtotal);

  var discRow = document.getElementById('pay-discount-row');
  if (totals.discount > 0) {
    discRow.style.display = 'flex';
    document.getElementById('pay-discount').textContent = '- ' + formatPrice(totals.discount);
  } else {
    discRow.style.display = 'none';
  }

  var shipEl = document.getElementById('pay-shipping');
  if (totals.shippingCost === 0) {
    shipEl.innerHTML = '<span style="color:#1a6b3a;font-weight:700">Grátis</span>';
  } else {
    shipEl.textContent = formatPrice(totals.shippingCost);
  }

  document.getElementById('pay-total').textContent = formatPrice(totals.total);

  // Preencher parcelas
  buildParcelas(totals.total);
}

// =========================================
// PARCELAS
// =========================================
function buildParcelas(total) {
  var sel = document.getElementById('card-parcelas');
  sel.innerHTML = '';
  var maxParcelas = 12;
  for (var i = 1; i <= maxParcelas; i++) {
    var val = total / i;
    var opt = document.createElement('option');
    opt.value = i;
    if (i === 1) {
      opt.textContent = '1x de ' + formatPrice(total) + ' (à vista)';
    } else {
      opt.textContent = i + 'x de ' + formatPrice(val) + ' sem juros';
    }
    sel.appendChild(opt);
  }
}

// =========================================
// TABS
// =========================================
function switchTab(tab) {
  document.querySelectorAll('.pay-tab').forEach(function (btn) {
    btn.classList.toggle('active', btn.dataset.tab === tab);
  });
  document.querySelectorAll('.pay-form').forEach(function (f) {
    f.style.display = 'none';
  });
  var target = document.getElementById('form-' + tab);
  if (target) target.style.display = 'block';

  if (tab === 'pix') initPix();
  if (tab === 'boleto') initBoleto();
}

// =========================================
// CARTÃO: máscaras e prévia
// =========================================
function updateCardPreview() {
  var num  = document.getElementById('card-number').value || '';
  var name = document.getElementById('card-name').value   || '';
  var exp  = document.getElementById('card-exp').value    || '';

  document.getElementById('cd-number').textContent = num  || '•••• •••• •••• ••••';
  document.getElementById('cd-name').textContent   = (name.toUpperCase() || 'SEU NOME AQUI');
  document.getElementById('cd-exp').textContent    = exp  || 'MM/AA';

  // Detectar bandeira
  var brand = document.getElementById('cd-brand');
  var rawNum = num.replace(/\D/g, '');
  if (/^4/.test(rawNum)) {
    brand.innerHTML = '<span style="font-size:0.7rem;font-weight:700;color:#1a1f71;letter-spacing:0.5px">VISA</span>';
  } else if (/^5[1-5]/.test(rawNum) || /^2[2-7]/.test(rawNum)) {
    brand.innerHTML = '<span style="font-size:0.7rem;font-weight:700;color:#eb001b;letter-spacing:0.5px">MASTER</span>';
  } else if (/^3[47]/.test(rawNum)) {
    brand.innerHTML = '<span style="font-size:0.7rem;font-weight:700;color:#007bc1;letter-spacing:0.5px">AMEX</span>';
  } else if (/^6011|^65|^64[4-9]/.test(rawNum)) {
    brand.innerHTML = '<span style="font-size:0.7rem;font-weight:700;color:#ff6600;letter-spacing:0.5px">ELO</span>';
  } else {
    brand.innerHTML = '';
  }
}

function maskCardNumber(el) {
  el.addEventListener('input', function () {
    var v = this.value.replace(/\D/g, '').slice(0, 16);
    this.value = v.replace(/(.{4})/g, '$1 ').trim();
    updateCardPreview();
  });
}

function maskCardExp(el) {
  el.addEventListener('input', function () {
    var v = this.value.replace(/\D/g, '');
    if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2, 4);
    this.value = v;
    updateCardPreview();
  });
}

// =========================================
// PIX
// =========================================
var pixTimerInterval = null;
var pixSecondsLeft = 30 * 60;

function initPix() {
  var totals = calcTotals();
  var code = 'DIASSNKRS' + Math.random().toString(36).slice(2, 10).toUpperCase() + totals.total.toFixed(2).replace('.', '');
  document.getElementById('pix-code-text').textContent = code;
  drawQrCode(code);

  if (pixTimerInterval) clearInterval(pixTimerInterval);
  pixSecondsLeft = 30 * 60;
  updatePixTimer();
  pixTimerInterval = setInterval(function () {
    pixSecondsLeft--;
    if (pixSecondsLeft <= 0) {
      clearInterval(pixTimerInterval);
      document.getElementById('pix-timer').textContent = 'expirado';
      document.getElementById('pix-timer').style.color = '#e03';
    } else {
      updatePixTimer();
    }
  }, 1000);
}

function updatePixTimer() {
  var m = Math.floor(pixSecondsLeft / 60);
  var s = pixSecondsLeft % 60;
  document.getElementById('pix-timer').textContent =
    (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
}

function drawQrCode(text) {
  var canvas = document.getElementById('pix-qr-canvas');
  var ctx = canvas.getContext('2d');
  var size = 180;
  ctx.clearRect(0, 0, size, size);

  // Fundo branco
  ctx.fillStyle = '#fff';
  ctx.fillRect(0, 0, size, size);

  // Simular QR code com padrão de blocos pseudo-aleatório baseado no texto
  var cellSize = 6;
  var margin = 12;
  var cells = Math.floor((size - margin * 2) / cellSize);
  ctx.fillStyle = '#000';

  // Usar texto como seed para gerar padrão consistente
  var seed = 0;
  for (var k = 0; k < text.length; k++) seed += text.charCodeAt(k);

  function seededRandom(n) {
    return ((Math.sin(n + seed) * 10000) % 1 + 1) % 1;
  }

  for (var row = 0; row < cells; row++) {
    for (var col = 0; col < cells; col++) {
      var isEdge = row < 7 || row >= cells - 7 || col < 7 || col >= cells - 7;
      var isFinder = (row < 7 && col < 7) || (row < 7 && col >= cells - 7) || (row >= cells - 7 && col < 7);

      if (isFinder) {
        // Finder patterns (cantos)
        var fr = row < 7 ? row : row - (cells - 7);
        var fc = col < 7 ? col : col - (cells - 7);
        var inOuter = fr === 0 || fr === 6 || fc === 0 || fc === 6;
        var inInner = fr >= 2 && fr <= 4 && fc >= 2 && fc <= 4;
        if (inOuter || inInner) {
          ctx.fillRect(margin + col * cellSize, margin + row * cellSize, cellSize - 1, cellSize - 1);
        }
      } else if (seededRandom(row * cells + col) > 0.45) {
        ctx.fillRect(margin + col * cellSize, margin + row * cellSize, cellSize - 1, cellSize - 1);
      }
    }
  }
}

// =========================================
// BOLETO
// =========================================
function initBoleto() {
  var barcode = document.getElementById('boleto-barcode');
  // Gerar barras visuais
  var bars = '';
  var pattern = [3,1,4,1,5,9,2,6,5,3,5,8,9,7,9,3,2,3,8,4,6,2,6,4,3,3,8,3,2,7,9,5];
  for (var i = 0; i < pattern.length; i++) {
    var w = pattern[i];
    bars += '<div class="boleto-bar' + (i % 2 === 0 ? '' : ' boleto-bar-white') + '" style="width:' + w + 'px"></div>';
  }
  // Repeat to fill width
  for (var j = 0; j < 4; j++) {
    for (var k = 0; k < pattern.length; k++) {
      bars += '<div class="boleto-bar' + (k % 2 === 0 ? '' : ' boleto-bar-white') + '" style="width:' + Math.max(1, pattern[k] - 1) + 'px"></div>';
    }
  }
  barcode.innerHTML = bars;

  // Vencimento: amanhã + 3 dias
  var venc = new Date();
  venc.setDate(venc.getDate() + 3);
  document.getElementById('boleto-venc').textContent = venc.toLocaleDateString('pt-BR');
}

// =========================================
// FINALIZAR
// =========================================
function finalizar(method) {
  var cart = getCart();
  if (cart.length === 0) { showToast('Carrinho vazio'); return; }

  // Validações por método
  if (method === 'cartao') {
    var num  = document.getElementById('card-number').value.replace(/\D/g, '');
    var name = document.getElementById('card-name').value.trim();
    var exp  = document.getElementById('card-exp').value;
    var cvv  = document.getElementById('card-cvv').value.replace(/\D/g, '');
    var parc = document.getElementById('card-parcelas').value;

    if (num.length < 15 || num.length > 16) { showToast('Número de cartão inválido'); return; }
    if (name.split(' ').filter(Boolean).length < 2) { showToast('Digite o nome completo do titular'); return; }
    if (!/^\d{2}\/\d{2}$/.test(exp)) { showToast('Data de validade inválida'); return; }
    if (cvv.length < 3) { showToast('CVV inválido'); return; }
    if (!parc) { showToast('Selecione o número de parcelas'); return; }
  }

  // Mostrar tela de sucesso
  var orderNum = Math.floor(100000 + Math.random() * 900000);
  document.getElementById('success-num').textContent = orderNum;

  var infoMsg = '';
  if (method === 'cartao') {
    var parcelasVal = document.getElementById('card-parcelas').value;
    var totals = calcTotals();
    infoMsg = parcelasVal + 'x de ' + formatPrice(totals.total / parcelasVal) + ' no cartão';
  } else if (method === 'pix') {
    infoMsg = 'Aguardando confirmação do PIX';
  } else if (method === 'boleto') {
    infoMsg = 'Boleto gerado. Pague até o vencimento.';
  }
  document.getElementById('success-payment-info').textContent = infoMsg;

  // Limpar carrinho
  localStorage.removeItem('ad_cart');
  localStorage.removeItem('ad_checkout');

  if (pixTimerInterval) clearInterval(pixTimerInterval);

  document.getElementById('success-screen').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

// =========================================
// INIT
// =========================================
document.addEventListener('DOMContentLoaded', function () {
  var cart = getCart();
  if (cart.length === 0) {
    window.location.href = 'carrinho.html';
    return;
  }

  renderOrderSummary();

  // Header spacer
  var header = document.getElementById('site-header');
  if (header) {
    var h = header.offsetHeight;
    document.getElementById('header-spacer').style.height = h + 'px';
    var sidebar = document.getElementById('checkout-sidebar');
    if (sidebar) sidebar.style.top = (h + 16) + 'px';
    window.addEventListener('resize', function () {
      var h2 = header.offsetHeight;
      document.getElementById('header-spacer').style.height = h2 + 'px';
      if (sidebar) sidebar.style.top = (h2 + 16) + 'px';
    });
  }

  // Tabs
  document.querySelectorAll('.pay-tab').forEach(function (btn) {
    btn.addEventListener('click', function () { switchTab(btn.dataset.tab); });
  });

  // Cartão masks
  maskCardNumber(document.getElementById('card-number'));
  maskCardExp(document.getElementById('card-exp'));
  document.getElementById('card-cvv').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 4);
  });
  document.getElementById('card-name').addEventListener('input', function () {
    this.value = this.value.toUpperCase();
    updateCardPreview();
  });

  // Botões finalizar
  document.getElementById('btn-pay-cartao').addEventListener('click', function () { finalizar('cartao'); });
  document.getElementById('btn-pay-pix').addEventListener('click', function () { finalizar('pix'); });
  document.getElementById('btn-pay-boleto').addEventListener('click', function () { finalizar('boleto'); });

  // Copiar PIX
  document.getElementById('btn-copy-pix').addEventListener('click', function () {
    var code = document.getElementById('pix-code-text').textContent;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(code).then(function () { showToast('Código PIX copiado!'); });
    } else {
      showToast('Copie o código manualmente.');
    }
  });

  // Copiar boleto
  document.getElementById('btn-copy-boleto').addEventListener('click', function () {
    var code = document.getElementById('boleto-code').textContent;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(code).then(function () { showToast('Código de barras copiado!'); });
    } else {
      showToast('Copie o código manualmente.');
    }
  });

  // Imprimir boleto
  document.getElementById('btn-boleto-dl').addEventListener('click', function () {
    showToast('Abrindo para impressão...');
    setTimeout(function () { window.print(); }, 500);
  });

  // Tab inicial
  switchTab('cartao');
});
