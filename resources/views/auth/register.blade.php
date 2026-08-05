<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar Conta | Dias Sneakers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Montserrat', sans-serif;
      background: #fff;
      color: #000;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 48px 20px;
      -webkit-font-smoothing: antialiased;
    }
    .auth-panel {
      width: 100%;
      max-width: 500px;
      display: flex;
      flex-direction: column;
    }
    .auth-logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      font-weight: 400;
      letter-spacing: 0.24em;
      text-transform: uppercase;
      color: #000;
      text-decoration: none;
      display: block;
      margin-bottom: 36px;
    }
    .auth-heading {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 300;
      line-height: 1.1;
      margin-bottom: 6px;
    }
    .auth-sub {
      font-size: 0.75rem;
      color: #777;
      letter-spacing: 0.06em;
      margin-bottom: 32px;
      font-weight: 400;
    }
    .alert-error {
      background: #fff5f5;
      color: #c00;
      border-left: 2px solid #c00;
      padding: 10px 14px;
      font-size: 0.73rem;
      margin-bottom: 20px;
      letter-spacing: 0.03em;
    }
    .field { margin-bottom: 18px; }
    .field label {
      display: block;
      font-size: 0.65rem;
      font-weight: 600;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: #666;
      margin-bottom: 8px;
    }
    .field input, .field select {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid #c5bdb4;
      color: #111;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.88rem;
      font-weight: 400;
      padding: 10px 0;
      outline: none;
      transition: border-color 0.2s;
      -webkit-appearance: none;
    }
    .field input::placeholder { color: #ccc; }
    .field input:focus, .field select:focus { border-bottom-color: #000; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .pwd-field { position: relative; }
    .pwd-field input { padding-right: 30px; }
    .eye-btn {
      position: absolute;
      right: 0;
      top: 55%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #ccc;
      display: flex;
      transition: color 0.2s;
    }
    .eye-btn:hover { color: #000; }
    .pwd-rules {
      list-style: none;
      margin: 8px 0 12px;
      padding: 0;
      display: none;
      grid-template-columns: 1fr 1fr;
      gap: 4px 12px;
    }
    .pwd-rules.visible { display: grid; }
    .pwd-rules li {
      font-size: 0.65rem;
      color: #bbb;
      padding-left: 14px;
      position: relative;
      letter-spacing: 0.04em;
      font-weight: 300;
    }
    .pwd-rules li::before { content: '✗'; position: absolute; left: 0; color: #f87171; font-size: 0.6rem; }
    .pwd-rules li.ok { color: #666; }
    .pwd-rules li.ok::before { content: '✓'; color: #22c55e; }
    .cpf-feedback { display: block; font-size: 0.65rem; margin-top: 5px; }
    .cpf-ok { color: #22c55e; }
    .cpf-err { color: #c00; }
    .btn-main {
      width: 100%;
      background: #000;
      color: #fff;
      border: 1px solid #000;
      padding: 14px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.62rem;
      font-weight: 400;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      cursor: pointer;
      transition: background 0.25s, color 0.25s;
      margin-top: 10px;
      margin-bottom: 16px;
    }
    .btn-main:hover { background: #fff; color: #000; }
    .auth-divider {
      display: flex;
      align-items: center;
      gap: 14px;
      margin: 6px 0 16px;
      color: #ddd;
      font-size: 0.62rem;
      letter-spacing: 0.1em;
    }
    .auth-divider::before, .auth-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e8e4df;
    }
    .btn-outline {
      width: 100%;
      background: transparent;
      color: #000;
      border: 1px solid #d0c9c0;
      padding: 13px;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.62rem;
      font-weight: 400;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      cursor: pointer;
      transition: border-color 0.25s;
      text-decoration: none;
      display: block;
      text-align: center;
    }
    .btn-outline:hover { border-color: #000; }
    .auth-footer-links { margin-top: 28px; }
    .auth-footer-links a {
      font-size: 0.68rem;
      color: #bbb;
      text-decoration: none;
      letter-spacing: 0.08em;
      transition: color 0.2s;
    }
    .auth-footer-links a:hover { color: #000; }
  </style>
</head>
<body>

  <div class="auth-panel">
    <a href="{{ route('home') }}" class="auth-logo">Dias Sneakers</a>

    <h1 class="auth-heading">Criar conta</h1>
    <p class="auth-sub">Preencha os dados para se cadastrar</p>

    @if($errors->any())
      <div class="alert-error">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif

    <form action="{{ route('register.post') }}" method="POST">
      @csrf

      <div class="field">
        <label>Nome Completo *</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Seu nome completo" required autofocus>
      </div>

      <div class="field">
        <label>E-mail *</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="seu@email.com" required>
      </div>

      <div class="form-row">
        <div class="field">
          <label>ID / Emirates ID</label>
          <input type="text" name="id_document" value="{{ old('id_document') }}" placeholder="784-XXXX-XXXXXXX-X" maxlength="30">
        </div>
        <div class="field">
          <label>Phone</label>
          @include('partials.phone-input', ['value' => old('phone'), 'name' => 'phone', 'id' => 'phone'])
        </div>
      </div>

      <div class="field">
        <label>Data de Nascimento</label>
        <input type="date" name="birth_date" value="{{ old('birth_date') }}">
      </div>

      <div class="form-row">
        <div class="field">
          <label>Senha *</label>
          <div class="pwd-field">
            <input id="password" type="password" name="password" placeholder="••••••••" required maxlength="15">
            <button type="button" class="eye-btn" id="eye-pwd" aria-label="Mostrar senha">
              <svg id="eye-pwd-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
        <div class="field">
          <label>Confirmar Senha *</label>
          <div class="pwd-field">
            <input id="password-confirm" type="password" name="password_confirmation" placeholder="••••••••" required maxlength="15">
            <button type="button" class="eye-btn" id="eye-confirm" aria-label="Mostrar senha">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
      </div>

      <ul class="pwd-rules" id="pwd-rules">
        <li id="rule-upper">Uma letra maiúscula</li>
        <li id="rule-number">Um número</li>
        <li id="rule-special">Um caractere especial (!@#$...)</li>
        <li id="rule-max">No máximo 15 caracteres</li>
      </ul>

      <button type="submit" class="btn-main">Criar Conta</button>
    </form>

    <div class="auth-divider">ou</div>

    <a href="{{ route('login') }}" class="btn-outline">Já tenho conta</a>

    <div class="auth-footer-links">
      <a href="{{ route('home') }}">← Voltar à loja</a>
    </div>
  </div>

<script>

(function () {
  var pwd      = document.getElementById('password');
  var rulesBox = document.getElementById('pwd-rules');
  var checks   = {
    'rule-upper':   function(v){ return /[A-Z]/.test(v); },
    'rule-number':  function(v){ return /[0-9]/.test(v); },
    'rule-special': function(v){ return /[\W_]/.test(v); },
    'rule-max':     function(v){ return v.length <= 15; },
  };

  pwd.addEventListener('focus', function(){ rulesBox.classList.add('visible'); });
  pwd.addEventListener('blur',  function(){ if (!pwd.value) rulesBox.classList.remove('visible'); });
  pwd.addEventListener('input', function () {
    var v = pwd.value;
    Object.keys(checks).forEach(function(id) {
      document.getElementById(id).classList.toggle('ok', checks[id](v));
    });
  });

  var eyeIconSvgs = {
    open:   '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
    closed: '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>',
  };

  function makeEyeToggle(btnId, inputEl) {
    var btn = document.getElementById(btnId);
    var icon = btn.querySelector('svg');
    var visible = false;
    btn.addEventListener('click', function () {
      visible = !visible;
      inputEl.type = visible ? 'text' : 'password';
      icon.innerHTML = eyeIconSvgs[visible ? 'closed' : 'open'];
    });
  }

  makeEyeToggle('eye-pwd',     document.getElementById('password'));
  makeEyeToggle('eye-confirm', document.getElementById('password-confirm'));
})();
</script>
</body>
</html>
