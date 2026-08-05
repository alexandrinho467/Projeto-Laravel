<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova senha | Dias Sneakers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Montserrat', sans-serif; background: #fff; color: #000; min-height: 100vh; display: flex; -webkit-font-smoothing: antialiased; }
    .auth-image { flex: 1; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f5f0eb; }
    .auth-image img { width: 75%; max-width: 440px; object-fit: contain; display: block; }
    .auth-image-brand { position: absolute; bottom: 40px; left: 40px; font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; font-weight: 400; letter-spacing: 0.2em; text-transform: uppercase; color: #000; }
    .auth-panel { width: 400px; flex-shrink: 0; display: flex; flex-direction: column; justify-content: center; padding: 56px 48px; background: #fff; border-left: 1px solid #e8e4df; }
    .auth-logo { font-family: 'Cormorant Garamond', serif; font-size: 1rem; font-weight: 400; letter-spacing: 0.24em; text-transform: uppercase; color: #000; text-decoration: none; display: block; margin-bottom: 40px; }
    .auth-heading { font-family: 'Cormorant Garamond', serif; font-size: 2.2rem; font-weight: 300; line-height: 1.1; margin-bottom: 8px; }
    .auth-sub { font-size: 0.7rem; color: #999; letter-spacing: 0.06em; margin-bottom: 36px; font-weight: 300; }
    .alert-error { background: #fff5f5; color: #c00; border-left: 2px solid #c00; padding: 10px 14px; font-size: 0.75rem; margin-bottom: 20px; }
    .field { margin-bottom: 18px; }
    .field label { display: block; font-size: 0.6rem; font-weight: 500; letter-spacing: 0.18em; text-transform: uppercase; color: #888; margin-bottom: 8px; }
    .field input { width: 100%; background: transparent; border: none; border-bottom: 1px solid #d0c9c0; color: #000; font-family: 'Montserrat', sans-serif; font-size: 0.86rem; font-weight: 300; padding: 10px 0; outline: none; transition: border-color 0.2s; }
    .field input::placeholder { color: #ccc; }
    .field input:focus { border-bottom-color: #000; }
    .field-hint { font-size: 0.65rem; color: #bbb; margin-top: 5px; letter-spacing: 0.04em; line-height: 1.5; }
    .btn-main { width: 100%; background: #000; color: #fff; border: 1px solid #000; padding: 14px; font-family: 'Montserrat', sans-serif; font-size: 0.62rem; font-weight: 400; letter-spacing: 0.22em; text-transform: uppercase; cursor: pointer; transition: background 0.25s, color 0.25s; margin-top: 8px; margin-bottom: 0; }
    .btn-main:hover { background: #fff; color: #000; }
    .auth-footer-links { margin-top: 28px; }
    .auth-footer-links a { font-size: 0.68rem; color: #bbb; text-decoration: none; letter-spacing: 0.08em; transition: color 0.2s; }
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

    <h1 class="auth-heading">Crie sua<br>nova senha.</h1>
    <p class="auth-sub">Escolha uma senha forte para proteger sua conta.</p>

    @if($errors->any())
      <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('senha.redefinir') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="field">
        <label>E-mail</label>
        <input type="email" name="email" value="{{ request('email') }}" placeholder="seu@email.com" required>
      </div>

      <div class="field">
        <label>Nova senha</label>
        <input type="password" name="password" placeholder="••••••••" required>
        <p class="field-hint">Mín. 6 caracteres · 1 maiúscula · 1 número · 1 caractere especial</p>
      </div>

      <div class="field">
        <label>Confirmar senha</label>
        <input type="password" name="password_confirmation" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn-main">Salvar nova senha</button>
    </form>

    <div class="auth-footer-links">
      <a href="{{ route('login') }}">← Voltar ao login</a>
    </div>
  </div>

</body>
</html>
