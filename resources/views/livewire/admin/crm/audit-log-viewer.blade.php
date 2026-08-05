<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Auditoria</h1>
  </div>

  @if($logs->isEmpty())
    <div style="text-align:center;padding:60px;color:#697386">
      <div style="font-size:2rem;margin-bottom:12px">🛡️</div>
      <div style="font-weight:600;color:#374151">Nenhuma atividade registrada ainda</div>
    </div>
  @else
    <table class="admin-table">
      <thead>
        <tr><th>Usuário</th><th>Ação</th><th>IP</th><th>Quando</th></tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
        <tr>
          <td>{{ $log->user?->name ?? 'Sistema' }}</td>
          <td style="color:#697386">{{ $log->description }}</td>
          <td style="color:#9CA3AF;font-size:.8rem">{{ $log->ip_address ?? '—' }}</td>
          <td style="color:#697386;font-size:.85rem">{{ $log->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div style="margin-top:18px">{{ $logs->links() }}</div>
  @endif
</div>
