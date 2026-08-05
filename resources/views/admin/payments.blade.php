@extends('layouts.admin')
@section('title', 'Pagamentos | Admin')
@section('content')
<div class="admin-topbar">
  <h1 class="admin-title">Pagamentos</h1>
  <a href="https://dashboard.stripe.com" target="_blank" rel="noopener" class="btn-secondary">Abrir Stripe Dashboard ↗</a>
</div>

@if($error)
  <div class="alert-error">Não foi possível carregar dados da Stripe: {{ $error }}</div>
@else
  <div class="stat-grid" style="margin-bottom:32px">
    <div class="stat-card">
      <div class="label">Saldo disponível</div>
      <div class="value" style="font-size:1.3rem">AED {{ number_format($available / 100, 2, '.', ',') }}</div>
    </div>
    <div class="stat-card">
      <div class="label">Saldo pendente</div>
      <div class="value" style="font-size:1.3rem">AED {{ number_format($pending / 100, 2, '.', ',') }}</div>
    </div>
    <div class="stat-card">
      <div class="label">Pagamentos recentes</div>
      <div class="value">{{ $payments->count() }}</div>
    </div>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Valor</th>
        <th>Status</th>
        <th>Método</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
      @forelse($payments as $p)
        <tr>
          <td style="font-family:monospace;font-size:.8rem;color:#697386">{{ $p->id }}</td>
          <td style="font-weight:600">AED {{ number_format($p->amount / 100, 2, '.', ',') }}</td>
          <td>
            @php
              $badge = match($p->status) {
                'succeeded' => 'badge-green',
                'processing', 'requires_action', 'requires_confirmation', 'requires_capture' => 'badge-yellow',
                'canceled' => 'badge-red',
                default => 'badge-blue',
              };
            @endphp
            <span class="badge {{ $badge }}">{{ $p->status }}</span>
          </td>
          <td style="color:#697386;font-size:.85rem">{{ implode(', ', $p->payment_method_types) }}</td>
          <td style="color:#697386;font-size:.85rem">{{ date('d/m/Y H:i', $p->created) }}</td>
        </tr>
      @empty
        <tr><td colspan="5" style="text-align:center;color:#666;padding:32px">Nenhum pagamento encontrado.</td></tr>
      @endforelse
    </tbody>
  </table>
@endif
@endsection
