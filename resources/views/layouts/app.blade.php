<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dias Sneakers')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
  @stack('styles')
</head>
<body>

  <!-- Announcement Bar -->
  <div class="announcement-bar">
    Frete grátis em compras acima de AED 1,500 &nbsp;·&nbsp; Parcele em até 12x sem juros
  </div>

  <!-- Header -->
  <header id="site-header">
    <div class="header-top">
      <!-- Left: Search -->
      <div class="header-left">
        <div class="header-search-wrap" id="search-wrap">
          <button class="header-search-btn" id="search-toggle-btn" type="button">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <span class="search-btn-label">Buscar</span>
          </button>
          <form class="header-search-form" id="search-form" action="{{ route('search') }}" method="GET">
            <input type="text" name="q" class="header-search-input" id="search-input"
              placeholder="Buscar marca, modelo..." autocomplete="off">
            <button type="submit" class="header-search-submit">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
            <button type="button" class="header-search-close" id="search-close-btn">&#x2715;</button>
          </form>
        </div>
      </div>

      <!-- Center: Logo -->
      <div class="logo">
        <a href="{{ route('home') }}" class="logo-text">Dias Sneakers</a>
      </div>

      <!-- Right: Account + Cart -->
      <div class="header-right">
        @auth
          <a href="{{ route('account.dashboard') }}" class="header-icon-btn" title="Minha Conta">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Conta
          </a>
          @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="header-link--admin">Admin</a>
          @endif
        @else
          <a href="{{ route('login') }}" class="header-icon-btn" title="Entrar">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Entrar
          </a>
        @endauth
        <button class="header-icon-btn" id="cart-toggle-btn" title="Carrinho">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          Carrinho
          <span class="cart-count" id="cart-badge" style="display:none">0</span>
        </button>
      </div>
    </div>

    <!-- Navigation -->
    <div class="header-nav">
      <nav>
        <a href="{{ route('home') }}">Novidades</a>
        <a href="{{ route('masculino') }}">Masculino</a>
        <a href="{{ route('feminino') }}">Feminino</a>
        <a href="{{ route('marcas') }}">Marcas</a>
        <a href="#">Promoções</a>
      </nav>
    </div>
  </header>

  <div id="header-spacer"></div>

  @yield('content')

  <!-- Cart Overlay -->
  <div class="cart-overlay" id="cart-overlay"></div>

  <!-- Cart Panel -->
  <aside class="cart-panel" id="cart-panel">
    <div class="cart-panel-header">
      <h3>Carrinho</h3>
      <button class="close-cart" id="close-cart">&#x2715;</button>
    </div>
    <div class="cart-items" id="cart-items-panel">
      <div class="cart-empty">Seu carrinho está vazio.</div>
    </div>
    <div class="cart-footer" id="cart-footer" style="display:none">
      <div class="cart-total"><span>Total</span><span id="cart-total-value">R$ 0,00</span></div>
      <a href="{{ route('cart') }}" class="cart-checkout-btn">Finalizar Compra</a>
    </div>
  </aside>

  <div class="toast" id="toast"></div>

  <!-- LGPD Cookie Banner -->
  <div class="lgpd-banner" id="lgpd-banner">
    <div class="lgpd-text">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="flex-shrink:0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      <span>Usamos cookies para melhorar sua experiência e processar pedidos. Ao continuar, você concorda com nossa
        <a href="{{ route('privacidade') }}">Política de Privacidade</a> em conformidade com a LGPD.
      </span>
    </div>
    <div class="lgpd-actions">
      <button class="lgpd-btn lgpd-btn--reject" id="lgpd-reject">Rejeitar</button>
      <button class="lgpd-btn lgpd-btn--accept" id="lgpd-accept">Aceitar</button>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="footer-newsletter">
      <span class="footer-newsletter-label">Receba novidades e ofertas exclusivas</span>
      <div class="newsletter-form">
        <input type="email" placeholder="seu@email.com">
        <button type="button">Inscrever</button>
      </div>
    </div>

    <div class="footer-middle">
      <div class="footer-col">
        <h4>Dias Sneakers</h4>
        <p>Fundada em 2025, nascida da paixão por calçados de alto desempenho e estilo atemporal.</p>
      </div>
      <div class="footer-col">
        <h4>Navegação</h4>
        <ul>
          <li><a href="{{ route('home') }}">Novidades</a></li>
          <li><a href="{{ route('masculino') }}">Masculino</a></li>
          <li><a href="{{ route('feminino') }}">Feminino</a></li>
          <li><a href="{{ route('marcas') }}">Marcas</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Ajuda</h4>
        <ul>
          <li><a href="#">Minha Conta</a></li>
          <li><a href="#">Rastrear Pedido</a></li>
          <li><a href="#">Devoluções</a></li>
          <li><a href="#">Guia de Tamanhos</a></li>
          <li><a href="{{ route('privacidade') }}">Política de Privacidade</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contato &amp; Pagamento</h4>
        <a href="mailto:suporte@alexandredias.com.br" class="support-email">suporte@alexandredias.com.br</a>
        <div class="payment-icons">
          <span class="payment-icon">Visa</span>
          <span class="payment-icon">Master</span>
          <span class="payment-icon">PIX</span>
          <span class="payment-icon">Boleto</span>
          <span class="payment-icon">Elo</span>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; {{ date('Y') }} Dias Sneakers. Todos os direitos reservados.</p>
      <p>Frete via Correios · Pagamentos seguros via Stripe</p>
    </div>
  </footer>

  <script>
    const ROUTES = {
      cartAdd:    '{{ route('cart.add') }}',
      cartRemove: '{{ route('cart.remove') }}',
      cartCount:  '{{ route('cart.count') }}',
      cartQty:    '{{ route('cart.qty') }}',
    };
    const CSRF = '{{ csrf_token() }}';
  </script>
  <script>
  (function () {
    const toggleBtn = document.getElementById('search-toggle-btn');
    const closeBtn  = document.getElementById('search-close-btn');
    const form      = document.getElementById('search-form');
    const input     = document.getElementById('search-input');

    function openSearch() {
      form.classList.add('open');
      toggleBtn.style.display = 'none';
      setTimeout(() => input.focus(), 50);
    }

    function closeSearch() {
      form.classList.remove('open');
      toggleBtn.style.display = '';
      input.value = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSearch);
    if (closeBtn)  closeBtn.addEventListener('click', closeSearch);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeSearch();
    });
  })();
  </script>
  <script src="{{ asset('assets/js/cart-panel.js') }}"></script>
  <script>
    (function () {
      var header  = document.getElementById('site-header');
      var spacer  = document.getElementById('header-spacer');
      var overlay = document.getElementById('cart-overlay');
      var panel   = document.getElementById('cart-panel');
      var badge   = document.getElementById('cart-badge');

      function syncSpacer() { spacer.style.height = header.offsetHeight + 'px'; }
      syncSpacer();
      window.addEventListener('resize', syncSpacer);

      // Hide-on-scroll
      var lastY = 0;
      window.addEventListener('scroll', function () {
        var y = window.scrollY;
        if (y > lastY && y > 120) {
          header.classList.add('header-hidden');
        } else {
          header.classList.remove('header-hidden');
        }
        lastY = y;
      }, { passive: true });

      // Cart toggle
      var cartBtn = document.getElementById('cart-toggle-btn');
      if (cartBtn) cartBtn.addEventListener('click', function () {
        panel.classList.add('open');
        overlay.classList.add('open');
        if (typeof loadCartPanel === 'function') loadCartPanel();
      });
      document.getElementById('close-cart').addEventListener('click', function () {
        panel.classList.remove('open');
        overlay.classList.remove('open');
      });
      overlay.addEventListener('click', function () {
        panel.classList.remove('open');
        overlay.classList.remove('open');
      });

      // Update badge
      window.updateCartBadge = function(count) {
        if (!badge) return;
        if (count > 0) {
          badge.textContent = count;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      };

      // Initial count
      fetch(ROUTES.cartCount).then(r => r.json()).then(d => {
        if (d.count) updateCartBadge(d.count);
      }).catch(() => {});
    })();
  </script>
  @stack('scripts')
  <script>
  (function () {
    const banner = document.getElementById('lgpd-banner');
    if (!banner) return;

    if (localStorage.getItem('lgpd_consent')) {
      banner.classList.add('hidden');
      return;
    }

    document.getElementById('lgpd-accept').addEventListener('click', function () {
      localStorage.setItem('lgpd_consent', 'accepted');
      banner.classList.add('hidden');
    });

    document.getElementById('lgpd-reject').addEventListener('click', function () {
      localStorage.setItem('lgpd_consent', 'rejected');
      banner.classList.add('hidden');
    });
  })();
  </script>
</body>
</html>
