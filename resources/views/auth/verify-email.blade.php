<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verificar E-mail | Dias Sneakers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
  <style>
    body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:#0a0a0a;padding:20px;font-family:'Inter',sans-serif}
    .auth-box{background:#111;border:1px solid #1f1f1f;border-radius:16px;padding:40px;width:100%;max-width:460px;text-align:center;text-transform:uppercase}
    .auth-logo{font-size:1.4rem;font-weight:800;margin-bottom:32px;color:#fff}
    .auth-logo span{color:#f97316}
    .icon-wrap{width:64px;height:64px;background:#1a1a1a;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px}
    .auth-title{font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:12px}
    .auth-desc{font-size:.85rem;color:#888;line-height:1.6;margin-bottom:28px;text-transform:none}
    .auth-desc strong{color:#fff}
    .btn-resend{display:inline-block;background:#f97316;color:#000;border:none;padding:13px 28px;border-radius:8px;font-weight:700;font-size:.9rem;cursor:pointer;text-transform:uppercase;letter-spacing:.05em}
    .btn-resend:hover{background:#ea6c0f}
    .alert-ok{background:#052e16;color:#4ade80;padding:10px 14px;border-radius:8px;font-size:.83rem;margin-bottom:20px}
    .divider{border:none;border-top:1px solid #1f1f1f;margin:24px 0}
    .auth-footer{font-size:.82rem;color:#555}
    .auth-footer a{color:#f97316;text-decoration:none}
  </style>
</head>
<body>
  <div class="auth-box">
    <div class="auth-logo">Dias <span>Sneakers</span></div>

    <div class="icon-wrap">
      <svg width="28" height="28" fill="none" stroke="#f97316" stroke-width="2" viewBox="0 0 24 24">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22,6 12,13 2,6"/>
      </svg>
    </div>

    <h1 class="auth-title">Confirme seu e-mail</h1>
    <p class="auth-desc">
      Enviamos um link de verificação para <strong>{{ Auth::user()->email }}</strong>.
      Acesse sua caixa de entrada e clique no link para ativar sua conta.
    </p>

    @if(session('resent'))
      <div class="alert-ok">✓ E-mail reenviado com sucesso!</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
      @csrf
      <button type="submit" class="btn-resend">Reenviar e-mail</button>
    </form>

    <hr class="divider">
    <div class="auth-footer">
      <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Sair da conta
      </a>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
  </div>
</body>
</html>
