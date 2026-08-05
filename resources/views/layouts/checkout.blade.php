<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Checkout | Dias Sneakers')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
  @stack('styles')
</head>
<body>
  <header id="site-header" style="position:fixed;top:0;left:0;right:0;z-index:1000;background:#fff;">
    <!-- Logo row -->
    <div style="display:flex;align-items:center;justify-content:center;height:64px;border-bottom:1px solid #e8e4df;">
      <a href="{{ route('home') }}" style="font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:500;letter-spacing:0.28em;text-transform:uppercase;color:#000;text-decoration:none;">Dias Sneakers</a>
    </div>
    <!-- Progress row -->
    <div class="checkout-progress">
      <div class="progress-step @yield('step1_class')">
        <div class="step-circle">@yield('step1_num', '1')</div>
        <div class="step-label">Carrinho</div>
      </div>
      <div class="step-line"></div>
      <div class="progress-step @yield('step2_class')">
        <div class="step-circle">@yield('step2_num', '2')</div>
        <div class="step-label">Identificação</div>
      </div>
      <div class="step-line"></div>
      <div class="progress-step @yield('step3_class')">
        <div class="step-circle">@yield('step3_num', '3')</div>
        <div class="step-label">Entrega</div>
      </div>
      <div class="step-line"></div>
      <div class="progress-step @yield('step4_class')">
        <div class="step-circle">@yield('step4_num', '4')</div>
        <div class="step-label">Pagamento</div>
      </div>
    </div>
    <div style="height:1px;background:#e8e4df;margin-top:12px"></div>
  </header>
  <div id="header-spacer"></div>

  @yield('content')

  <div class="toast" id="toast"></div>
  <script>
    (function () {
      var header = document.getElementById('site-header');
      var spacer = document.getElementById('header-spacer');
      function syncSpacer() { spacer.style.height = header.offsetHeight + 'px'; }
      syncSpacer();
      window.addEventListener('resize', syncSpacer);
      window.addEventListener('load', syncSpacer);
      document.fonts && document.fonts.ready.then(syncSpacer);
    })();
  </script>
  @stack('scripts')
</body>
</html>
