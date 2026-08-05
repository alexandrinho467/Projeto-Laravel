@extends('layouts.admin')
@section('title', 'Cupons | Admin')
@section('content')

<div class="admin-topbar">
  <h1 class="admin-title">Cupons de desconto</h1>
  <a href="{{ route('admin.cupons.create') }}" class="btn-primary">+ Novo cupom</a>
</div>

@if($cupons->isEmpty())
  <div style="text-align:center;padding:60px;color:#697386">
    <div style="font-size:2rem;margin-bottom:12px">🏷️</div>
    <div style="font-weight:600;color:#374151">Nenhum cupom criado ainda</div>
    <a href="{{ route('admin.cupons.create') }}" class="btn-primary" style="margin-top:20px;display:inline-block">Criar primeiro cupom</a>
  </div>
@else
<table class="admin-table">
  <thead>
    <tr>
      <th>Código</th>
      <th>Desconto</th>
      <th>Pedido mínimo</th>
      <th>Usos</th>
      <th>Validade</th>
      <th>Status</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($cupons as $cupon)
    <tr>
      <td>
        <span style="font-family:monospace;font-size:.82rem;font-weight:700;color:#3730A3;letter-spacing:.08em;background:#EEF2FF;padding:4px 10px;border-radius:6px;border:1px solid #C7D2FE">{{ $cupon->code }}</span>
      </td>
      <td>
        @if($cupon->type === 'percent')
          <span style="color:#059669;font-weight:600">{{ number_format($cupon->value, 0) }}% off</span>
        @else
          <span style="color:#059669;font-weight:600">AED {{ number_format($cupon->value, 2, '.', ',') }} off</span>
        @endif
      </td>
      <td style="color:#697386">
        @if($cupon->min_order)
          AED {{ number_format($cupon->min_order, 2, '.', ',') }}
        @else
          <span style="color:#9CA3AF">—</span>
        @endif
      </td>
      <td style="color:#697386">
        {{ $cupon->uses }}
        @if($cupon->max_uses)
          / {{ $cupon->max_uses }}
        @else
          <span style="color:#9CA3AF">/ ilimitado</span>
        @endif
      </td>
      <td style="color:#aaa;font-size:.85rem">
        @if($cupon->expires_at)
          @if($cupon->expires_at->isPast())
            <span style="color:#991B1B">{{ $cupon->expires_at->format('d/m/Y') }} (expirado)</span>
          @else
            {{ $cupon->expires_at->format('d/m/Y') }}
          @endif
        @else
          <span style="color:#9CA3AF">Sem validade</span>
        @endif
      </td>
      <td>
        @if($cupon->active)
          <span class="badge badge-green">Ativo</span>
        @else
          <span class="badge badge-red">Inativo</span>
        @endif
      </td>
      <td>
        <div style="display:flex;gap:8px;align-items:center">
          <a href="{{ route('admin.cupons.edit', $cupon) }}" class="btn-secondary" style="padding:6px 12px;font-size:.78rem">Editar</a>

          <form action="{{ route('admin.cupons.toggle', $cupon) }}" method="POST">
            @csrf
            <button class="btn-secondary" style="padding:6px 12px;font-size:.78rem">
              {{ $cupon->active ? 'Desativar' : 'Ativar' }}
            </button>
          </form>

          <form action="{{ route('admin.cupons.destroy', $cupon) }}" method="POST"
            onsubmit="return confirm('Excluir o cupom {{ $cupon->code }}?')">
            @csrf @method('DELETE')
            <button class="btn-danger" style="padding:6px 12px;font-size:.78rem">Excluir</button>
          </form>
        </div>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif

@endsection
