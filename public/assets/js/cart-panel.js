// cart-panel.js — Dias Sneakers luxury layout

function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}

function updateCartBadge(count) {
  const badge = document.getElementById('cart-badge');
  if (!badge) return;
  if (count > 0) {
    badge.textContent = count;
    badge.style.display = 'flex';
  } else {
    badge.style.display = 'none';
  }
}

function formatAED(value) {
  return 'AED ' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function loadCartPanel() {
  fetch(ROUTES.cartCount)
    .then(r => r.json())
    .then(d => {
      updateCartBadge(d.count || 0);

      const itemsEl  = document.getElementById('cart-items-panel');
      const footerEl = document.getElementById('cart-footer');
      if (!itemsEl) return;

      if (!d.count || d.count === 0) {
        itemsEl.innerHTML = '<div class="cart-empty">Seu carrinho está vazio.</div>';
        if (footerEl) footerEl.style.display = 'none';
        return;
      }

      if (d.items && d.items.length > 0) {
        let html = '';
        let total = 0;
        d.items.forEach(item => {
          const lineTotal = item.price * item.qty;
          total += lineTotal;
          html += `<div class="cart-item">
            <img src="${item.img}" alt="${item.product_name}">
            <div class="cart-item-info">
              <div class="ci-brand">${item.product_brand || ''}</div>
              <div class="ci-name">${item.product_name}</div>
              <div class="ci-size">Tam: ${item.size}</div>
              <div class="ci-price">${formatAED(lineTotal)}</div>
            </div>
            <button class="remove-item" onclick="removeFromPanel('${item.key}')">&#x2715;</button>
          </div>`;
        });
        itemsEl.innerHTML = html;
        if (footerEl) {
          footerEl.style.display = 'block';
          const tv = document.getElementById('cart-total-value');
          if (tv) tv.textContent = formatAED(d.cart_total || total);
        }
      } else {
        itemsEl.innerHTML = '<div class="cart-empty">Seu carrinho está vazio.</div>';
        if (footerEl) footerEl.style.display = 'none';
      }
    })
    .catch(() => {});
}

function removeFromPanel(key) {
  fetch(ROUTES.cartRemove, {
    method: 'POST',
    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
    body: JSON.stringify({key})
  })
  .then(r => r.json())
  .then(() => loadCartPanel())
  .catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
  loadCartPanel();
});
