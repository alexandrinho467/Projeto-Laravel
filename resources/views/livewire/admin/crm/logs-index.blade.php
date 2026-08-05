<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Logs</h1>
  </div>

  <div style="display:flex;gap:12px;margin-bottom:18px;flex-wrap:wrap">
    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Buscar por cliente..." style="flex:1;min-width:220px;background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
    @if($availableChannels->isNotEmpty())
    <select wire:model.live="channel" style="background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
      <option value="">Todos os canais</option>
      @foreach($availableChannels as $canal)
        <option value="{{ $canal }}">{{ ['whatsapp' => 'WhatsApp', 'instagram' => 'Instagram', 'email' => 'E-mail'][$canal] ?? $canal }}</option>
      @endforeach
    </select>
    @endif
    @if(auth()->user()->isAdmin())
    <select wire:model.live="vendedorId" style="background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
      <option value="">Todos os vendedores</option>
      @foreach($staff as $member)
        <option value="{{ $member->id }}">{{ $member->name }}</option>
      @endforeach
    </select>
    @endif
  </div>

  @if($logs->isEmpty())
    <div style="text-align:center;padding:60px;color:#697386">
      <div style="font-size:2rem;margin-bottom:12px">📋</div>
      <div style="font-weight:600;color:#374151">Nenhuma interação encontrada</div>
    </div>
  @else
    <table class="admin-table">
      <thead>
        <tr>
          <th>Vendedor</th>
          <th>Cliente</th>
          <th>Canal</th>
          <th>Mensagens</th>
          <th>Primeira interação</th>
          <th>Última interação</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
        @php
          $contact = $contacts->get($log->crm_contact_id);
          $vendedor = $log->user_id ? $vendedores->get($log->user_id) : null;
        @endphp
        <tr>
          <td>{{ $vendedor?->name ?? '—' }}</td>
          <td>{{ $contact?->name ?? '—' }}</td>
          <td><span class="badge badge-blue">{{ ['whatsapp' => 'WhatsApp', 'instagram' => 'Instagram', 'email' => 'E-mail'][$log->channel] ?? $log->channel }}</span></td>
          <td style="color:#697386">{{ $log->total }}</td>
          <td style="color:#697386;white-space:nowrap">{{ \Illuminate\Support\Carbon::parse($log->primeira_interacao)->format('d/m/Y H:i') }}</td>
          <td style="color:#697386;white-space:nowrap">{{ \Illuminate\Support\Carbon::parse($log->ultima_interacao)->format('d/m/Y H:i') }}</td>
          <td style="display:flex;gap:8px">
            <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem;white-space:nowrap" wire:click="verHistorico({{ $log->crm_contact_id }}, {{ $log->user_id ?? 'null' }}, '{{ $log->channel }}')">Ver Histórico</button>
            @if($contact)
              <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem;white-space:nowrap" wire:click="verNegociacoes({{ $contact->id }}, {{ $log->user_id ?? 'null' }})">Ver Negociações</button>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div style="margin-top:18px">{{ $logs->links() }}</div>
  @endif

  @if($showHistoryModal && $historyContact)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showHistoryModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:640px;max-height:80vh;overflow-y:auto">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:4px;color:#1A1F36">Histórico com {{ $historyContact->name }}</h2>
      <div style="color:#697386;font-size:.82rem;margin-bottom:18px">Vendedor: {{ $historyVendedor?->name ?? '—' }}</div>

      @if(empty($historyMessages) || collect($historyMessages)->isEmpty())
        <div style="color:#697386;font-size:.85rem">Nenhuma interação encontrada.</div>
      @else
        <table class="admin-table">
          <thead>
            <tr><th>Data</th><th>Hora</th><th>Canal</th><th>Direção</th><th>Conteúdo</th></tr>
          </thead>
          <tbody>
            @foreach($historyMessages as $message)
            <tr>
              <td style="white-space:nowrap">{{ $message->occurred_at->format('d/m/Y') }}</td>
              <td style="white-space:nowrap">{{ $message->occurred_at->format('H:i') }}</td>
              <td><span class="badge badge-blue">{{ $message->channel_label }}</span></td>
              <td style="color:#697386">{{ $message->direction === 'enviada' ? 'Enviada' : 'Recebida' }}</td>
              <td style="color:#697386;max-width:280px">{{ \Illuminate\Support\Str::limit($message->content, 60) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif

      <div style="display:flex;gap:12px;margin-top:14px">
        <button type="button" class="btn-secondary" wire:click="$set('showHistoryModal', false)">Fechar</button>
      </div>
    </div>
  </div>
  @endif

  @if($showDealsModal && $viewingContact)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showDealsModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:520px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:4px;color:#1A1F36">Negociações de {{ $viewingContact->name }}</h2>
      <div style="color:#697386;font-size:.82rem;margin-bottom:18px">
        {{ $viewingContact->email ?: $viewingContact->phone ?: '—' }}
        @if($dealsVendedor)
          · Vendedor: {{ $dealsVendedor->name }}
        @endif
      </div>

      @if($viewingContact->deals->isEmpty())
        <div style="color:#697386;font-size:.85rem;margin-bottom:18px">Nenhuma negociação {{ $dealsVendedor ? 'deste vendedor ' : '' }}para este cliente.</div>
      @else
        <table class="admin-table" style="margin-bottom:8px">
          <thead>
            <tr><th>Título</th><th>Valor</th><th>Estágio</th><th></th></tr>
          </thead>
          <tbody>
            @foreach($viewingContact->deals as $deal)
            <tr>
              <td>{{ $deal->title }}</td>
              <td>{{ $deal->value_formatted }}</td>
              <td><span class="badge badge-blue">{{ $deal->stage_label }}</span></td>
              <td>
                <a href="{{ route('admin.crm.conversations.show', $deal) }}" class="btn-secondary" style="padding:6px 12px;font-size:.78rem;white-space:nowrap">Ver Conversa de Negociação</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif

      <div style="display:flex;gap:12px;margin-top:14px">
        <button type="button" class="btn-secondary" wire:click="$set('showDealsModal', false)">Fechar</button>
      </div>
    </div>
  </div>
  @endif
</div>
