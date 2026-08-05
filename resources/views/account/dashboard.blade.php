@extends('layouts.account')
@section('title', 'Minha Conta | Dias Sneakers')
@section('content')

<div class="ac-page-title">Olá, {{ explode(' ', auth()->user()->name)[0] }}.</div>
<div class="ac-page-subtitle">Bem-vindo à sua área exclusiva Dias Sneakers.</div>

@if(auth()->user()->hasCrmAccess())
<div class="ac-card" style="border-color:#000;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px">
  <div>
    <div style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:0.18em;color:#888;margin-bottom:8px">Equipe</div>
    <div style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:#111">Painel CRM</div>
    <div style="font-family:'Montserrat',sans-serif;font-size:0.78rem;color:#777;margin-top:4px">Contatos, leads e pipeline de vendas.</div>
  </div>
  <a href="{{ route('admin.crm.dashboard') }}" class="ac-btn ac-btn-primary">Ir para o CRM</a>
</div>
@endif

<div class="ac-card">
  <div class="ac-card-title">Pedidos Recentes</div>

  @forelse($orders as $order)
    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 0;border-bottom:1px solid #f0ece8;gap:16px">
      <div style="display:flex;align-items:center;gap:16px">
        <div style="width:42px;height:42px;background:#f5f0eb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg width="18" height="18" fill="none" stroke="#888" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
        </div>
        <div>
          <div style="font-family:'Montserrat',sans-serif;font-size:0.82rem;font-weight:600;color:#111;margin-bottom:3px">Pedido #{{ $order->id }}</div>
          <div style="font-family:'Montserrat',sans-serif;font-size:0.7rem;color:#888;font-weight:400">
            {{ $order->created_at->format('d/m/Y') }} &bull; {{ $order->items->count() }} item(s)
          </div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:20px;flex-shrink:0">
        <div style="text-align:right">
          <div style="font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:500;color:#111">{{ $order->total_formatted }}</div>
          <div style="margin-top:4px">
            @php
              $statusColors = [
                'delivered' => ['bg'=>'#f0fdf4','color'=>'#16a34a','border'=>'#bbf7d0'],
                'cancelled'  => ['bg'=>'#fff5f5','color'=>'#c00','border'=>'#fecaca'],
              ];
              $sc = $statusColors[$order->status] ?? ['bg'=>'#fafaf8','color'=>'#777','border'=>'#e8e4df'];
            @endphp
            <span style="font-family:'Montserrat',sans-serif;font-size:0.62rem;font-weight:600;padding:3px 10px;letter-spacing:0.08em;text-transform:uppercase;
              background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }}">
              {{ $order->status_label }}
            </span>
          </div>
        </div>
        <a href="{{ route('account.order.show', $order->id) }}" class="ac-btn ac-btn-ghost" style="padding:8px 18px;font-size:0.62rem">
          Ver
        </a>
      </div>
    </div>
  @empty
    <div style="text-align:center;padding:40px 0">
      <svg width="40" height="40" fill="none" stroke="#ccc" stroke-width="1.2" viewBox="0 0 24 24" style="margin:0 auto 14px;display:block"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
      <p style="font-family:'Montserrat',sans-serif;font-size:0.78rem;color:#aaa;margin-bottom:20px;letter-spacing:0.06em">Você ainda não fez nenhum pedido.</p>
      <a href="{{ route('home') }}" class="ac-btn ac-btn-primary">Explorar produtos</a>
    </div>
  @endforelse

  @if($orders->count() > 0)
    <a href="{{ route('account.orders') }}"
       style="display:inline-flex;align-items:center;gap:6px;margin-top:20px;font-family:'Montserrat',sans-serif;font-size:0.68rem;font-weight:500;letter-spacing:0.1em;text-transform:uppercase;color:#555;text-decoration:none;transition:color 0.2s"
       onmouseover="this.style.color='#000'" onmouseout="this.style.color='#555'">
      Ver todos os pedidos
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  <div class="ac-card">
    <div class="ac-card-title">Meus Dados</div>
    <div style="display:flex;flex-direction:column;gap:8px">
      <div style="font-family:'Montserrat',sans-serif;font-size:0.8rem;color:#555">{{ auth()->user()->email }}</div>
      @if(auth()->user()->phone)
        <div style="font-family:'Montserrat',sans-serif;font-size:0.8rem;color:#555">{{ auth()->user()->phone }}</div>
      @endif
    </div>
    <a href="{{ route('account.profile') }}" class="ac-btn ac-btn-ghost" style="margin-top:24px;font-size:0.62rem">
      Editar dados
    </a>
  </div>

  <div class="ac-card" style="border-color:#000">
    <div class="ac-card-title" style="color:#555;border-color:#e8e4df">Cupom de Boas-vindas</div>
    <div style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:600;letter-spacing:0.12em;color:#111;margin-bottom:6px">BEMVINDO10</div>
    <div style="font-family:'Montserrat',sans-serif;font-size:0.72rem;color:#777;margin-bottom:24px">10% de desconto na sua primeira compra</div>
    <a href="{{ route('home') }}" class="ac-btn ac-btn-primary" style="font-size:0.62rem">
      Usar agora
    </a>
  </div>
</div>

@endsection
