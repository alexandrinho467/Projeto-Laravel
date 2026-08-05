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
// MINI RESUMO
// =========================================
function renderMiniSummary() {
  var cart = getCart();
  var saved = getCheckout();
  var rows = document.getElementById('mini-rows');
  if (!rows) return;

  if (cart.length === 0) {
    rows.innerHTML = '<div style="font-size:0.82rem;color:#aaa;text-align:center;padding:16px 0">Carrinho vazio</div>';
    return;
  }

  var subtotal = cart.reduce(function (s, x) { return s + x.price * x.qty; }, 0);
  var discount = 0;
  if (saved.coupon) {
    if (saved.coupon.type === 'percent') {
      discount = subtotal * (saved.coupon.value / 100);
    }
  }

  var shippingCost = null;
  if (saved.shipping) {
    if (saved.coupon && saved.coupon.type === 'shipping') {
      shippingCost = 0;
    } else if (subtotal >= 1500) {
      shippingCost = 0;
    } else {
      shippingCost = saved.shipping.price;
    }
  }

  var total = subtotal - discount + (shippingCost || 0);

  var html = cart.map(function (item) {
    return '<div class="mini-summary-item">' +
      '<img src="' + item.img + '" alt="' + item.name + '">' +
      '<div class="mini-summary-item-info">' +
        '<div class="mini-name">' + item.brand + ' ' + item.name + '</div>' +
        '<div class="mini-meta">Tam. ' + item.size + ' &bull; Qtd. ' + item.qty + '</div>' +
      '</div>' +
      '<div class="mini-price">' + formatPrice(item.price * item.qty) + '</div>' +
    '</div>';
  }).join('');

  html += '<div class="mini-summary-totals">';
  html += '<div class="mini-total-row"><span>Subtotal</span><span>' + formatPrice(subtotal) + '</span></div>';
  if (discount > 0) {
    html += '<div class="mini-total-row" style="color:#1a6b3a"><span>Desconto</span><span>- ' + formatPrice(discount) + '</span></div>';
  }
  if (shippingCost !== null) {
    html += '<div class="mini-total-row"><span>Frete</span><span>' +
      (shippingCost === 0 ? '<span style="color:#1a6b3a;font-weight:700">Grátis</span>' : formatPrice(shippingCost)) +
    '</span></div>';
  }
  html += '<div class="mini-total-row total"><span>Total</span><span>' + formatPrice(total) + '</span></div>';
  html += '</div>';

  rows.innerHTML = html;
}

// =========================================
// MÁSCARAS
// =========================================
function maskCpf(el) {
  el.addEventListener('input', function () {
    var v = this.value.replace(/\D/g, '');
    if (v.length > 9) v = v.slice(0, 3) + '.' + v.slice(3, 6) + '.' + v.slice(6, 9) + '-' + v.slice(9, 11);
    else if (v.length > 6) v = v.slice(0, 3) + '.' + v.slice(3, 6) + '.' + v.slice(6);
    else if (v.length > 3) v = v.slice(0, 3) + '.' + v.slice(3);
    this.value = v;
  });
}

function maskPhone(el) {
  el.addEventListener('input', function () {
    var v = this.value.replace(/\D/g, '');
    if (v.length > 10) v = '(' + v.slice(0, 2) + ') ' + v.slice(2, 7) + '-' + v.slice(7, 11);
    else if (v.length > 6) v = '(' + v.slice(0, 2) + ') ' + v.slice(2, 6) + '-' + v.slice(6);
    else if (v.length > 2) v = '(' + v.slice(0, 2) + ') ' + v.slice(2);
    this.value = v;
  });
}

function maskDate(el) {
  el.addEventListener('input', function () {
    var v = this.value.replace(/\D/g, '');
    if (v.length > 4) v = v.slice(0, 2) + '/' + v.slice(2, 4) + '/' + v.slice(4, 8);
    else if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
    this.value = v;
  });
}

// =========================================
// VALIDAÇÃO
// =========================================
function showErr(id, show) {
  var el = document.getElementById(id);
  if (el) el.style.display = show ? 'block' : 'none';
}

function setInvalid(fieldId, hasError) {
  var el = document.getElementById(fieldId);
  if (!el) return;
  if (hasError) el.classList.add('invalid');
  else el.classList.remove('invalid');
}

function validateCpf(cpf) {
  var d = cpf.replace(/\D/g, '');
  if (d.length !== 11 || /^(\d)\1+$/.test(d)) return false;
  var sum = 0;
  for (var i = 0; i < 9; i++) sum += parseInt(d[i]) * (10 - i);
  var r = 11 - (sum % 11);
  if (r > 9) r = 0;
  if (r !== parseInt(d[9])) return false;
  sum = 0;
  for (var j = 0; j < 10; j++) sum += parseInt(d[j]) * (11 - j);
  var r2 = 11 - (sum % 11);
  if (r2 > 9) r2 = 0;
  return r2 === parseInt(d[10]);
}

function validateDate(val) {
  if (!/^\d{2}\/\d{2}\/\d{4}$/.test(val)) return false;
  var parts = val.split('/');
  var d = parseInt(parts[0]), m = parseInt(parts[1]), y = parseInt(parts[2]);
  if (m < 1 || m > 12 || d < 1 || d > 31 || y < 1900 || y > 2010) return false;
  return true;
}

function validateForm() {
  var nome  = document.getElementById('field-nome').value.trim();
  var cpf   = document.getElementById('field-cpf').value;
  var tel   = document.getElementById('field-tel').value.replace(/\D/g, '');
  var nasc  = document.getElementById('field-nasc').value;

  var ok = true;

  var erroNome = nome.split(' ').filter(Boolean).length < 2;
  showErr('err-nome', erroNome); setInvalid('field-nome', erroNome);
  if (erroNome) ok = false;

  var erroCpf = !validateCpf(cpf);
  showErr('err-cpf', erroCpf); setInvalid('field-cpf', erroCpf);
  if (erroCpf) ok = false;

  var erroTel = tel.length < 10;
  showErr('err-tel', erroTel); setInvalid('field-tel', erroTel);
  if (erroTel) ok = false;

  var erroNasc = !validateDate(nasc);
  showErr('err-nasc', erroNasc); setInvalid('field-nasc', erroNasc);
  if (erroNasc) ok = false;

  return ok;
}

// =========================================
// CONTINUAR
// =========================================
function continuar() {
  if (!validateForm()) {
    showToast('Preencha todos os campos corretamente');
    return;
  }

  var nome = document.getElementById('field-nome').value.trim();
  var cpf  = document.getElementById('field-cpf').value;
  var tel  = document.getElementById('field-tel').value;
  var nasc = document.getElementById('field-nasc').value;

  saveCheckout({ user: { nome: nome, cpf: cpf, tel: tel, nasc: nasc } });
  window.location.href = 'pagamento.html';
}

// =========================================
// INIT
// =========================================
document.addEventListener('DOMContentLoaded', function () {
  // Se não tem carrinho, redireciona
  var cart = getCart();
  if (cart.length === 0) {
    window.location.href = 'carrinho.html';
    return;
  }

  renderMiniSummary();

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

  // Máscaras
  maskCpf(document.getElementById('field-cpf'));
  maskPhone(document.getElementById('field-tel'));
  maskDate(document.getElementById('field-nasc'));

  // Preencher dados salvos
  var saved = getCheckout();
  if (saved.user) {
    document.getElementById('field-nome').value = saved.user.nome || '';
    document.getElementById('field-cpf').value  = saved.user.cpf  || '';
    document.getElementById('field-tel').value  = saved.user.tel  || '';
    document.getElementById('field-nasc').value = saved.user.nasc || '';
  }

  // Botão continuar
  document.getElementById('btn-continuar').addEventListener('click', continuar);

  // Login simulado
  document.getElementById('btn-login').addEventListener('click', function () {
    var email = document.getElementById('login-email').value.trim();
    if (!email || !email.includes('@')) {
      showToast('Digite um e-mail válido'); return;
    }
    saveCheckout({ email: email, loginType: 'conta' });
    showToast('Login realizado! Preencha os dados abaixo.');
  });

  // Visitante simulado
  document.getElementById('btn-guest').addEventListener('click', function () {
    var email = document.getElementById('guest-email').value.trim();
    if (!email || !email.includes('@')) {
      showToast('Digite um e-mail válido'); return;
    }
    saveCheckout({ email: email, loginType: 'visitante' });
    showToast('Continuando como visitante. Preencha os dados abaixo.');
  });

  // Remover erros ao digitar
  ['field-nome','field-cpf','field-tel','field-nasc'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', function () {
        el.classList.remove('invalid');
      });
    }
  });
});
