@extends('layouts.admin')
@section('title', 'Relatório de Estoque | Admin')
@section('content')

<div class="admin-topbar">
  <h1 class="admin-title">Movimentações de estoque</h1>
  <a href="{{ route('admin.estoque') }}" class="btn-secondary">← Voltar</a>
</div>

{{-- Filtros --}}
<form method="GET" style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap">
  <input type="text" name="produto" value="{{ request('produto') }}" placeholder="Buscar produto..."
    style="background:#111;border:1px solid #2a2a2a;color:#fff;padding:9px 14px;border-radius:8px;font-size:.88rem;outline:none;width:220px">
  <select name="tipo" style="background:#111;border:1px solid #2a2a2a;color:#fff;padding:9px 14px;border-radius:8px;font-size:.88rem;outline:none">
    <option value="">Todos os tipos</option>
    <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entradas</option>
    <option value="saida"   {{ request('tipo') === 'saida'   ? 'selected' : '' }}>Saídas</option>
  </select>
  <button type="submit" class="btn-primary" style="padding:9px 20px">Filtrar</button>
  @if(request('produto') || request('tipo'))
    <a href="{{ route('admin.estoque.relatorio') }}" class="btn-secondary" style="padding:9px 20px">Limpar</a>
  @endif
</form>

@if($movements->isEmpty())
  <div style="text-align:center;padding:60px;color:#555">
    <div style="font-size:2rem;margin-bottom:12px">📦</div>
    <div style="color:#888">Nenhuma movimentação registrada ainda.</div>
  </div>
@else
  <table class="admin-table">
    <thead>
      <tr>
        <th>Data</th>
        <th>Tipo</th>
        <th>Produto</th>
        <th>Tamanho</th>
        <th>Qntd</th>
        <th>Pedido</th>
        <th>Obs</th>
      </tr>
    </thead>
    <tbody>
      @foreach($movements as $m)
      <tr>
        <td style="color:#666;font-size:.82rem;white-space:nowrap">
          {{ $m->created_at->format('d/m/Y H:i') }}
        </td>
        <td>
          @if($m->type === 'entrada')
            <span style="display:inline-flex;align-items:center;gap:5px;background:#14532d;color:#4ade80;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:700">
              ↑ Entrada
            </span>
          @else
            <span style="display:inline-flex;align-items:center;gap:5px;background:#450a0a;color:#f87171;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:700">
              ↓ Saída
            </span>
          @endif
        </td>
        <td>
          <div style="font-weight:600;font-size:.9rem;color:#fff">{{ $m->product_name }}</div>
          @if($m->product_brand)
            <div style="font-size:.75rem;color:#666">{{ $m->product_brand }}</div>
          @endif
        </td>
        <td>
          <span style="background:#1f1f1f;color:#ccc;padding:3px 10px;border-radius:6px;font-size:.82rem;font-weight:600">
            {{ $m->size }}
          </span>
        </td>
        <td>
          <span style="font-size:1rem;font-weight:800;color:{{ $m->type === 'entrada' ? '#4ade80' : '#f87171' }}">
            {{ $m->type === 'entrada' ? '+' : '-' }}{{ $m->qty }}
          </span>
        </td>
        <td style="font-size:.85rem;color:#aaa">
          @if($m->order_id)
            <a href="{{ route('admin.orders.show', $m->order_id) }}" style="color:#f97316;text-decoration:none">
              Pedido #{{ $m->order_id }}
            </a>
          @else
            <span style="color:#444">—</span>
          @endif
        </td>
        <td style="font-size:.8rem;color:#666">
          {{ $m->notes ?? '—' }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:20px">
    {{ $movements->withQueryString()->links() }}
  </div>
@endif

@endsection
