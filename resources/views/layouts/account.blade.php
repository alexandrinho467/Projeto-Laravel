<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Minha Conta | Dias Sneakers')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
  <style>
    /* ── ACCOUNT LAYOUT ── */
    .ac-shell {
      display: flex;
      min-height: 100vh;
      background: #faf9f7;
    }

    /* ── SIDEBAR ── */
    .ac-sidebar {
      width: 260px;
      flex-shrink: 0;
      background: #fff;
      border-right: 1px solid #e8e4df;
      padding: 48px 0 40px;
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
    }

    .ac-user-block {
      padding: 0 28px 32px;
      border-bottom: 1px solid #e8e4df;
      margin-bottom: 24px;
    }

    .ac-user-initial {
      width: 48px;
      height: 48px;
      background: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem;
      font-weight: 500;
      color: #fff;
      margin-bottom: 14px;
      letter-spacing: 0.06em;
    }

    .ac-user-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.15rem;
      font-weight: 500;
      color: #111;
      line-height: 1.3;
      margin-bottom: 3px;
    }

    .ac-user-email {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.68rem;
      font-weight: 400;
      color: #888;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ac-nav { flex: 1; }

    .ac-nav a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 28px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.72rem;
      font-weight: 500;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #888;
      text-decoration: none;
      transition: color 0.2s, background 0.2s;
      border-left: 2px solid transparent;
    }

    .ac-nav a:hover {
      color: #111;
      background: #f5f2ef;
    }

    .ac-nav a.active {
      color: #111;
      background: #f5f2ef;
      border-left-color: #000;
    }

    .ac-nav a svg { width: 15px; height: 15px; flex-shrink: 0; }

    .ac-nav-divider {
      height: 1px;
      background: #e8e4df;
      margin: 16px 28px;
    }

    .ac-logout-form {
      padding: 0 20px;
    }

    .ac-logout-btn {
      width: 100%;
      background: none;
      border: 1px solid #e8e4df;
      color: #888;
      cursor: pointer;
      font-size: 0.68rem;
      font-weight: 500;
      font-family: 'Montserrat', sans-serif;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      padding: 11px 16px;
      text-align: left;
      transition: border-color 0.2s, color 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ac-logout-btn:hover {
      border-color: #000;
      color: #000;
    }

    /* ── MAIN CONTENT ── */
    .ac-main {
      flex: 1;
      padding: 56px 64px;
      min-width: 0;
    }

    .ac-page-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.2rem;
      font-weight: 500;
      letter-spacing: 0.04em;
      color: #111;
      margin-bottom: 6px;
    }

    .ac-page-subtitle {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.75rem;
      font-weight: 400;
      color: #777;
      margin-bottom: 40px;
      letter-spacing: 0.04em;
    }

    /* ── CARDS ── */
    .ac-card {
      background: #fff;
      border: 1px solid #e8e4df;
      padding: 28px 32px;
      margin-bottom: 20px;
    }

    .ac-card-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.62rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.18em;
      color: #888;
      margin-bottom: 24px;
      padding-bottom: 14px;
      border-bottom: 1px solid #e8e4df;
    }

    /* ── ALERTS ── */
    .ac-alert-success {
      background: #f0fdf4;
      border-left: 2px solid #16a34a;
      color: #16a34a;
      padding: 12px 16px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.78rem;
      font-weight: 500;
      margin-bottom: 28px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ac-alert-error {
      background: #fff5f5;
      border-left: 2px solid #c00;
      color: #c00;
      padding: 12px 16px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.78rem;
      font-weight: 500;
      margin-bottom: 28px;
    }

    /* ── FORMS ── */
    .ac-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .ac-form-group { display: flex; flex-direction: column; gap: 7px; }
    .ac-form-group.full { grid-column: 1 / -1; }

    .ac-form-group label {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.62rem;
      font-weight: 600;
      color: #555;
      text-transform: uppercase;
      letter-spacing: 0.14em;
    }

    .ac-form-group input,
    .ac-form-group select,
    .ac-form-group textarea {
      background: #faf9f7;
      border: 1px solid #d0c9c0;
      color: #111;
      padding: 11px 14px;
      font-size: 0.86rem;
      font-weight: 400;
      font-family: 'Montserrat', sans-serif;
      outline: none;
      transition: border-color 0.2s;
    }

    .ac-form-group input:focus,
    .ac-form-group select:focus,
    .ac-form-group textarea:focus { border-color: #000; }

    .ac-form-group input::placeholder { color: #bbb; }

    /* ── BUTTONS ── */
    .ac-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 28px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.65rem;
      font-weight: 500;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.25s, color 0.25s, border-color 0.25s;
      border: 1px solid #000;
    }

    .ac-btn-primary { background: #000; color: #fff; border-color: #000; }
    .ac-btn-primary:hover { background: #fff; color: #000; }

    .ac-btn-ghost { background: transparent; border-color: #d0c9c0; color: #555; }
    .ac-btn-ghost:hover { border-color: #000; color: #000; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .ac-shell { flex-direction: column; }
      .ac-sidebar {
        width: 100%;
        height: auto;
        position: static;
        flex-direction: row;
        flex-wrap: wrap;
        padding: 16px 20px;
        border-right: none;
        border-bottom: 1px solid #e8e4df;
        gap: 0;
        align-items: center;
      }
      .ac-user-block { padding: 0; border-bottom: none; margin-bottom: 0; margin-right: 20px; }
      .ac-user-initial { width: 36px; height: 36px; font-size: 1.1rem; margin-bottom: 0; }
      .ac-user-name { font-size: 0.9rem; }
      .ac-user-email { display: none; }
      .ac-nav { display: flex; flex-wrap: wrap; }
      .ac-nav a { border-left: none; border-bottom: 2px solid transparent; padding: 10px 14px; }
      .ac-nav a.active { border-left: none; border-bottom-color: #000; background: none; }
      .ac-nav-divider { display: none; }
      .ac-logout-form { padding: 0 4px; }
      .ac-logout-btn { border: none; padding: 10px 14px; }
      .ac-main { padding: 32px 20px; }
      .ac-form-grid { grid-template-columns: 1fr; }
    }
  </style>
  @stack('styles')
</head>
<body>

  <!-- Announcement Bar -->
  <div class="announcement-bar">
    Frete grátis em compras acima de AED 1,500 &nbsp;·&nbsp; Parcele em até 12x sem juros
  </div>

  <!-- Header (idêntico ao site principal) -->
  <header id="site-header">
    <div class="header-top">
      <div class="header-left">
        <a href="{{ route('home') }}" class="header-search-btn" style="text-decoration:none">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
          Loja
        </a>
      </div>

      <div class="logo">
        <a href="{{ route('home') }}" class="logo-text">Dias Sneakers</a>
      </div>

      <div class="header-right">
        <a href="{{ route('account.dashboard') }}" class="header-icon-btn" style="text-decoration:none">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Conta
        </a>
        @if(auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="header-link--admin">Admin</a>
        @endif
      </div>
    </div>

  </header>

  <div id="header-spacer"></div>

  <!-- Account shell -->
  <div class="ac-shell">

    <!-- Sidebar -->
    <aside class="ac-sidebar">
      <div class="ac-user-block">
        <div class="ac-user-initial">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="ac-user-name">{{ auth()->user()->name }}</div>
        <div class="ac-user-email">{{ auth()->user()->email }}</div>
      </div>

      <nav class="ac-nav">
        <a href="{{ route('account.dashboard') }}" class="{{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          Dashboard
        </a>
        <a href="{{ route('account.orders') }}" class="{{ request()->routeIs('account.orders*') ? 'active' : '' }}">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Meus Pedidos
        </a>
        <a href="{{ route('account.profile') }}" class="{{ request()->routeIs('account.profile') ? 'active' : '' }}">
          <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Meus Dados
        </a>
        @if(auth()->user()->isAdmin())
          <div class="ac-nav-divider"></div>
          <a href="{{ route('admin.dashboard') }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            Área Admin
          </a>
        @endif
      </nav>

      <div class="ac-nav-divider"></div>

      <div class="ac-logout-form">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="ac-logout-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sair da conta
          </button>
        </form>
      </div>
    </aside>

    <!-- Conteúdo -->
    <main class="ac-main">
      @if(session('success'))
        <div class="ac-alert-success">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="ac-alert-error">{{ session('error') }}</div>
      @endif
      @yield('content')
    </main>

  </div>

  <script>
    (function () {
      var header = document.getElementById('site-header');
      var spacer = document.getElementById('header-spacer');
      function syncSpacer() { spacer.style.height = header.offsetHeight + 'px'; }
      syncSpacer();
      window.addEventListener('resize', syncSpacer);
    })();
  </script>
  @stack('scripts')
</body>
</html>
