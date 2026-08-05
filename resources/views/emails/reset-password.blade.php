<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redefinição de senha</title>
  <style>
    body { margin: 0; padding: 0; background: #f5f5f5; font-family: Arial, sans-serif; }
    .wrapper { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .header { background: #111; padding: 28px 40px; text-align: center; }
    .header .logo { font-size: 1.1rem; font-weight: 800; letter-spacing: .04em; color: #fff; }
    .header .logo span { color: #f97316; }
    .body { padding: 40px; }
    .greeting { font-size: 1rem; font-weight: 600; color: #111; margin-bottom: 12px; }
    .text { font-size: .88rem; color: #555; line-height: 1.7; margin-bottom: 28px; }
    .btn { display: block; width: fit-content; margin: 0 auto 28px; background: #f97316; color: #fff; text-decoration: none; padding: 14px 36px; border-radius: 4px; font-size: .85rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .link-fallback { font-size: .75rem; color: #aaa; text-align: center; word-break: break-all; line-height: 1.6; }
    .link-fallback a { color: #f97316; }
    .footer { background: #f5f5f5; padding: 20px 40px; text-align: center; font-size: .72rem; color: #aaa; border-top: 1px solid #eee; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <div class="logo">Dias <span>Sneakers</span></div>
    </div>
    <div class="body">
      <p class="greeting">Olá, {{ $userName }}!</p>
      <p class="text">
        Recebemos uma solicitação para redefinir a senha da sua conta.<br>
        Clique no botão abaixo para criar uma nova senha. O link é válido por <strong>60 minutos</strong>.
      </p>
      <a href="{{ $resetUrl }}" class="btn">Redefinir minha senha</a>
      <p class="link-fallback">
        Se o botão não funcionar, copie e cole este link no navegador:<br>
        <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
      </p>
    </div>
    <div class="footer">
      Se você não solicitou a redefinição de senha, ignore este e-mail. Sua senha permanece a mesma.
    </div>
  </div>
</body>
</html>
