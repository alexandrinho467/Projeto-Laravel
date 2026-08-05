<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar | Dias Sneakers</title>
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
      -webkit-font-smoothing: antialiased;
    }
    .auth-image {
      flex: 1;
      position: relative;
      overflow: hidden;
      background: #f5f0eb;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .auth-image img {
      width: 75%;
      max-width: 440px;
      object-fit: contain;
      display: block;
      position: relative;
      z-index: 1;
    }
    .auth-image-brand {
      position: absolute;
      bottom: 40px;
      left: 40px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.3rem;
      font-weight: 400;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: #000;
      z-index: 2;
    }
    .auth-panel {
      width: 400px;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 56px 48px;
      background: #fff;
      border-left: 1px solid #e8e4df;
    }
    .auth-logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.05rem;
      font-weight: 400;
      letter-spacing: 0.24em;
      text-transform: uppercase;
      color: #000;
      text-decoration: none;
      display: block;
      margin-bottom: 40px;
    }
    .auth-heading {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.2rem;
      font-weight: 300;
      line-height: 1.1;
      margin-bottom: 8px;
      color: #000;
    }
    .auth-sub {
      font-size: 0.76rem;
      color: #777;
      letter-spacing: 0.06em;
      margin-bottom: 36px;
      font-weight: 400;
    }
    .alert-error {
      background: #fff5f5;
      color: #c00;
      border-left: 2px solid #c00;
      padding: 10px 14px;
      font-size: 0.75rem;
      margin-bottom: 20px;
      letter-spacing: 0.04em;
    }
    .alert-success {
      background: #f0fff4;
      color: #276749;
      border-left: 2px solid #276749;
      padding: 10px 14px;
      font-size: 0.75rem;
      margin-bottom: 20px;
      letter-spacing: 0.04em;
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
    .field input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid #c5bdb4;
      color: #111;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.9rem;
      font-weight: 400;
      padding: 10px 0;
      outline: none;
      transition: border-color 0.2s;
    }
    .field input::placeholder { color: #ccc; }
    .field input:focus { border-bottom-color: #000; }
    .remember-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 6px 0 24px;
    }
    .remember-row input[type=checkbox] {
      width: 14px;
      height: 14px;
      accent-color: #000;
      cursor: pointer;
      flex-shrink: 0;
    }
    .remember-row label {
      font-size: 0.72rem;
      color: #666;
      cursor: pointer;
      font-weight: 400;
      letter-spacing: 0.04em;
    }
    .forgot-link {
      margin-left: auto;
      font-size: 0.68rem;
      color: #aaa;
      text-decoration: none;
      letter-spacing: 0.06em;
      transition: color 0.2s;
    }
    .forgot-link:hover { color: #000; }
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
      margin-bottom: 16px;
    }
    .btn-main:hover { background: #fff; color: #000; }
    .auth-divider {
      display: flex;
      align-items: center;
      gap: 14px;
      margin: 8px 0 16px;
      color: #ddd;
      font-size: 0.65rem;
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
      transition: border-color 0.25s, color 0.25s;
      text-decoration: none;
      display: block;
      text-align: center;
    }
    .btn-outline:hover { border-color: #000; color: #000; }
    .auth-footer-links {
      margin-top: 28px;
    }
    .auth-footer-links a {
      font-size: 0.68rem;
      color: #bbb;
      text-decoration: none;
      letter-spacing: 0.08em;
      transition: color 0.2s;
    }
    .auth-footer-links a:hover { color: #000; }
    @media (max-width: 768px) {
      .auth-image { display: none; }
      .auth-panel { width: 100%; border-left: none; padding: 48px 28px; }
    }
  </style>
</head>
<body>

  <div class="auth-image">
    <img src="{{ asset('assets/images/tenis 2.png') }}" alt="Dias Sneakers">
    <div class="auth-image-brand">Dias Sneakers</div>
  </div>

  <div class="auth-panel">
    <a href="{{ route('home') }}" class="auth-logo">Dias Sneakers</a>

    <h1 class="auth-heading">Bem-vindo<br>de volta.</h1>
    <p class="auth-sub">Entre na sua conta para continuar</p>

    @if(session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
      @csrf

      <div class="field">
        <label>E-mail</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="seu@email.com" autofocus required>
      </div>

      <div class="field">
        <label>Senha</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>

      <div class="remember-row">
        <input type="checkbox" name="remember" id="remember" value="1">
        <label for="remember">Lembrar de mim</label>
        <a href="{{ route('senha.esqueci') }}" class="forgot-link">Esqueceu a senha?</a>
      </div>

      <button type="submit" class="btn-main">Entrar</button>
    </form>

    <div class="auth-divider">ou</div>

    <a href="{{ route('register') }}" class="btn-outline">Criar conta</a>

    <div class="auth-footer-links">
      <a href="{{ route('home') }}">← Voltar à loja</a>
    </div>
  </div>

</body>
</html>
