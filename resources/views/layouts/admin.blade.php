<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin | Dias Sneakers')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{display:flex;min-height:100vh;background:#F6F9FC;color:#1A1F36;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;font-size:14px;-webkit-font-smoothing:antialiased;font-weight:400}

    /* ── Sidebar ── */
    .admin-sidebar{width:240px;background:#0A2540;flex-shrink:0;position:fixed;height:100vh;overflow-y:auto;display:flex;flex-direction:column}
    .admin-sidebar .brand{padding:20px 20px 16px;font-weight:700;font-size:.95rem;color:#fff;letter-spacing:-.01em;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:4px}
    .admin-sidebar .brand span{color:#635BFF}
    .nav-section{padding:14px 20px 4px;font-size:.64rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.28)}
    .admin-sidebar nav{display:block;padding:0 8px;flex:1}
    .admin-sidebar nav a{display:block;padding:8px 12px;color:rgba(255,255,255,.6);text-decoration:none;font-size:.855rem;font-weight:500;border-radius:6px;transition:all .15s;margin-bottom:1px}
    .admin-sidebar nav a:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.9)}
    .admin-sidebar nav a.active{background:rgba(99,91,255,.2);color:#fff}
    .nav-group{margin-bottom:1px}
    .nav-group summary{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;color:rgba(255,255,255,.6);font-size:.855rem;font-weight:500;border-radius:6px;cursor:pointer;list-style:none;user-select:none;transition:all .15s}
    .nav-group summary::-webkit-details-marker{display:none}
    .nav-group summary:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.9)}
    .nav-group.has-active summary{color:#fff}
    .nav-group summary .chevron{width:13px;height:13px;flex-shrink:0;opacity:.5;transition:transform .15s}
    .nav-group[open] summary .chevron{transform:rotate(180deg)}
    .nav-group-items{padding-left:12px;margin-top:1px}
    .nav-group-items a{padding:7px 12px 7px 16px;font-size:.83rem}
    .sidebar-footer{padding:8px 8px 16px;border-top:1px solid rgba(255,255,255,.08);margin-top:4px}
    .sidebar-footer a,.sidebar-footer button{display:flex;align-items:center;gap:9px;padding:8px 12px;color:rgba(255,255,255,.45);font-size:.855rem;font-weight:500;border-radius:6px;text-decoration:none;background:none;border:none;cursor:pointer;font-family:inherit;width:100%;transition:all .15s}
    .sidebar-footer a:hover,.sidebar-footer button:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.8)}
    .sidebar-footer svg{width:15px;height:15px;flex-shrink:0;opacity:.6}

    /* ── Main ── */
    .admin-main{margin-left:240px;flex:1;padding:28px 36px;min-height:100vh}
    .admin-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .admin-title{font-size:1.3rem;font-weight:700;color:#1A1F36;letter-spacing:-.02em}

    /* ── Buttons ── */
    .btn-primary{background:#635BFF;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-weight:600;font-size:.84rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:background .15s;font-family:inherit}
    .btn-primary:hover{background:#4F46E5;color:#fff}
    .btn-secondary{background:#fff;color:#374151;border:1px solid #E3E8EE;padding:8px 16px;border-radius:6px;cursor:pointer;font-weight:500;font-size:.84rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all .15s;font-family:inherit}
    .btn-secondary:hover{border-color:#C1CDD6;background:#F6F9FC;color:#1A1F36}
    .btn-danger{background:#fff;color:#B91C1C;border:1px solid #FECACA;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:.8rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;transition:background .15s;font-family:inherit}
    .btn-danger:hover{background:#FEF2F2}

    /* ── Tables ── */
    .admin-table{width:100%;border-collapse:collapse;font-size:.86rem;background:#fff;border-radius:8px;overflow:hidden;border:1px solid #E3E8EE}
    .admin-table th{text-align:left;padding:10px 16px;color:#697386;font-weight:600;background:#F7FAFC;border-bottom:1px solid #E3E8EE;text-transform:uppercase;font-size:.69rem;letter-spacing:.07em}
    .admin-table td{padding:12px 16px;border-bottom:1px solid #F1F5F9;vertical-align:middle;color:#1A1F36}
    .admin-table tr:last-child td{border-bottom:none}
    .admin-table tbody tr:hover td{background:#F7FAFC}

    /* ── Stat cards ── */
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:24px}
    .stat-card{background:#fff;border-radius:8px;padding:20px 22px;border:1px solid #E3E8EE}
    .stat-card .label{font-size:.71rem;color:#697386;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px}
    .stat-card .value{font-size:1.85rem;font-weight:700;color:#1A1F36;letter-spacing:-.02em}
    .stat-card .sub{font-size:.75rem;color:#697386;margin-top:4px}

    /* ── Forms ── */
    .form-group{margin-bottom:18px}
    .form-group label{display:block;font-size:.82rem;color:#374151;margin-bottom:6px;font-weight:500}
    .form-group input,.form-group select,.form-group textarea{width:100%;background:#fff;border:1px solid #D1D5DB;color:#1A1F36;padding:8px 12px;border-radius:6px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s}
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#635BFF;box-shadow:0 0 0 3px rgba(99,91,255,.12)}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}

    /* ── Badges ── */
    .badge{display:inline-block;padding:2px 10px;border-radius:20px;font-size:.74rem;font-weight:600}
    .badge-green{background:#D1FAE5;color:#065F46}
    .badge-yellow{background:#FEF3C7;color:#92400E}
    .badge-red{background:#FEE2E2;color:#991B1B}
    .badge-blue{background:#DBEAFE;color:#1D4ED8}

    /* ── Alerts ── */
    .alert-success{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.87rem}
    .alert-error{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.87rem}

    @media(max-width:768px){.admin-sidebar{display:none}.admin-main{margin-left:0;padding:20px}}
  </style>
  @stack('styles')
</head>
<body>
  <aside class="admin-sidebar">
    <div class="brand">Dias <span>Admin</span></div>
    <div style="padding:0 12px 12px">
      <livewire:admin.global-search />
    </div>
    @php
      $crmActive = request()->routeIs('admin.crm.dashboard') || request()->routeIs('admin.crm.contacts*') || request()->routeIs('admin.crm.pipeline') || request()->routeIs('admin.crm.tasks') || request()->routeIs('admin.crm.email-settings') || request()->routeIs('admin.crm.logs') || request()->routeIs('admin.crm.conversations.show') || request()->routeIs('admin.crm.lost-reasons') || request()->routeIs('admin.crm.messages') || request()->routeIs('admin.crm.settings') || request()->routeIs('admin.crm.audit');
      $catalogoActive = request()->routeIs('admin.produtos.*') || request()->routeIs('admin.estoque') || request()->routeIs('admin.estoque.relatorio');
      $vendasActive = request()->routeIs('admin.vendas') || request()->routeIs('admin.orders*');
      $financeiroActive = request()->routeIs('admin.payments.*');
      $configActive = request()->routeIs('admin.cupons*') || request()->routeIs('admin.settings*') || request()->routeIs('admin.team*');
    @endphp
    <nav>
      @if(auth()->user()->isAdmin())
      <div class="nav-section">Visão Geral</div>
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
      @endif

      <details class="nav-group {{ $crmActive ? 'has-active' : '' }}" @if($crmActive) open @endif>
        <summary>
          CRM
          <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </summary>
        <div class="nav-group-items">
          <a href="{{ route('admin.crm.dashboard') }}" class="{{ request()->routeIs('admin.crm.dashboard') ? 'active' : '' }}">Visão CRM</a>
          <a href="{{ route('admin.crm.contacts.index') }}" class="{{ request()->routeIs('admin.crm.contacts*') ? 'active' : '' }}">Contatos</a>
          <a href="{{ route('admin.crm.pipeline') }}" class="{{ request()->routeIs('admin.crm.pipeline') ? 'active' : '' }}">Pipeline</a>
          <a href="{{ route('admin.crm.tasks') }}" class="{{ request()->routeIs('admin.crm.tasks') ? 'active' : '' }}">Minhas Tarefas</a>
          <a href="{{ route('admin.crm.email-settings') }}" class="{{ request()->routeIs('admin.crm.email-settings') ? 'active' : '' }}">Meu E-mail</a>
          <a href="{{ route('admin.crm.logs') }}" class="{{ request()->routeIs('admin.crm.logs') || request()->routeIs('admin.crm.conversations.show') ? 'active' : '' }}">Logs</a>
          <a href="{{ route('admin.crm.contacts.index', ['showArchived' => 1]) }}" class="{{ request()->routeIs('admin.crm.contacts.index') && request('showArchived') ? 'active' : '' }}">Arquivo Morto</a>
          @if(auth()->user()->isAdmin())
          <a href="{{ route('admin.crm.contacts.export') }}">Exportar Contatos</a>
          <a href="{{ route('admin.crm.lost-reasons') }}" class="{{ request()->routeIs('admin.crm.lost-reasons') ? 'active' : '' }}">Motivos de Perda</a>
          <a href="{{ route('admin.crm.messages') }}" class="{{ request()->routeIs('admin.crm.messages') ? 'active' : '' }}">Modelos de Mensagens</a>
          <a href="{{ route('admin.crm.settings') }}" class="{{ request()->routeIs('admin.crm.settings') ? 'active' : '' }}">Configurações do CRM</a>
          <a href="{{ route('admin.crm.audit') }}" class="{{ request()->routeIs('admin.crm.audit') ? 'active' : '' }}">Auditoria</a>
          @endif
        </div>
      </details>

      @if(auth()->user()->isAdmin())
      <details class="nav-group {{ $catalogoActive ? 'has-active' : '' }}" @if($catalogoActive) open @endif>
        <summary>
          Catálogo
          <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </summary>
        <div class="nav-group-items">
          <a href="{{ route('admin.produtos.index') }}" class="{{ request()->routeIs('admin.produtos.index') ? 'active' : '' }}">Produtos</a>
          <a href="{{ route('admin.produtos.create') }}" class="{{ request()->routeIs('admin.produtos.create') ? 'active' : '' }}">Novo produto</a>
          <a href="{{ route('admin.estoque') }}" class="{{ request()->routeIs('admin.estoque') && !request()->routeIs('admin.estoque.relatorio') ? 'active' : '' }}">Estoque</a>
          <a href="{{ route('admin.estoque.relatorio') }}" class="{{ request()->routeIs('admin.estoque.relatorio') ? 'active' : '' }}">Movimentações</a>
        </div>
      </details>

      <details class="nav-group {{ $vendasActive ? 'has-active' : '' }}" @if($vendasActive) open @endif>
        <summary>
          Vendas
          <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </summary>
        <div class="nav-group-items">
          <a href="{{ route('admin.vendas') }}" class="{{ request()->routeIs('admin.vendas') ? 'active' : '' }}">Relatório de vendas</a>
          <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">Pedidos</a>
        </div>
      </details>

      <details class="nav-group {{ $financeiroActive ? 'has-active' : '' }}" @if($financeiroActive) open @endif>
        <summary>
          Financeiro
          <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </summary>
        <div class="nav-group-items">
          <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">Pagamentos</a>
          <a href="https://dashboard.stripe.com" target="_blank" rel="noopener">Stripe Dashboard ↗</a>
        </div>
      </details>

      <details class="nav-group {{ $configActive ? 'has-active' : '' }}" @if($configActive) open @endif>
        <summary>
          Configurações
          <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </summary>
        <div class="nav-group-items">
          <a href="{{ route('admin.cupons.index') }}" class="{{ request()->routeIs('admin.cupons*') ? 'active' : '' }}">Cupons</a>
          <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">Configurações</a>
          <a href="{{ route('admin.team.index') }}" class="{{ request()->routeIs('admin.team*') ? 'active' : '' }}">Equipe</a>
        </div>
      </details>
      @endif
    </nav>
    <div class="sidebar-footer">
      <a href="{{ route('home') }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Ver Site
      </a>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Sair
        </button>
      </form>
    </div>
  </aside>

  <main class="admin-main">
    @if(session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert-error">{{ session('error') }}</div>
    @endif
    @yield('content')
  </main>
  @stack('scripts')
</body>
</html>
