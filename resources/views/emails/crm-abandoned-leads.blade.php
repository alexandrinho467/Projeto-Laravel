<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leads abandonados no CRM</title>
  <style>
    body { margin: 0; padding: 0; background: #f5f5f5; font-family: Arial, sans-serif; color: #111; }
    .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }

    .header { background: #0A2540; padding: 28px 40px; }
    .logo { font-size: 1.1rem; font-weight: 800; letter-spacing: .04em; color: #fff; }
    .logo span { color: #635BFF; }

    .hero { background: #B91C1C; padding: 24px 40px; text-align: center; }
    .hero-title { font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0 0 4px; }
    .hero-sub { font-size: .82rem; color: rgba(255,255,255,.85); margin: 0; }

    .body { padding: 28px 40px; }
    .greeting { font-size: .9rem; color: #333; margin-bottom: 22px; line-height: 1.6; }

    .item-row { padding: 14px 16px; background: #F6F9FC; border: 1px solid #E3E8EE; border-radius: 8px; margin-bottom: 10px; }
    .item-contact { font-size: .88rem; font-weight: 700; color: #1A1F36; margin-bottom: 4px; }
    .item-desc { font-size: .82rem; color: #444; margin-bottom: 6px; }
    .item-due { font-size: .74rem; color: #B91C1C; font-weight: 600; }
    .item-link { display: inline-block; margin-top: 8px; font-size: .74rem; color: #635BFF; text-decoration: none; font-weight: 600; }

    .footer { background: #f5f5f5; padding: 18px 40px; text-align: center; font-size: .72rem; color: #aaa; border-top: 1px solid #eee; line-height: 1.7; }
  </style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="logo">Dias <span>Admin</span></div>
  </div>

  <div class="hero">
    <p class="hero-title">
      {{ $deals->count() === 1 ? '1 lead abandonado' : $deals->count() . ' leads abandonados' }}
    </p>
    <p class="hero-sub">Novos leads há mais de 24h sem primeiro contato.</p>
  </div>

  <div class="body">
    <p class="greeting">
      Olá, <strong>{{ explode(' ', $recipient->name)[0] }}</strong>. Os leads abaixo entraram no funil há mais de 24 horas e ainda estão em "Novo Lead" — ninguém assumiu ou fez o primeiro contato.
    </p>

    @foreach($deals as $deal)
    <div class="item-row">
      <div class="item-contact">{{ $deal->contact?->name ?? 'Contato removido' }}</div>
      <div class="item-desc">{{ $deal->title }} — {{ $deal->value_formatted }}</div>
      <div class="item-due">Entrou em: {{ $deal->created_at->format('d/m/Y H:i') }}</div>
      @if($deal->contact)
        <a href="{{ route('admin.crm.contacts.show', $deal->contact) }}" class="item-link">Ver contato →</a>
      @endif
    </div>
    @endforeach
  </div>

  <div class="footer">
    <strong>Dias Sneakers</strong> — Painel CRM
  </div>

</div>
</body>
</html>
