<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Contatos</h1>
    <div style="display:flex;gap:10px">
      @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.crm.contacts.export') }}" class="btn-secondary">Exportar CSV</a>
      @endif
      <button type="button" class="btn-primary" wire:click="openCreateModal">+ Novo Contato</button>
    </div>
  </div>

  <div style="display:flex;gap:12px;margin-bottom:18px;flex-wrap:wrap;align-items:center">
    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Buscar por nome, e-mail ou telefone..." style="flex:1;min-width:220px;background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
    <select wire:model.live="status" style="background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
      <option value="">Todos os status</option>
      <option value="lead">Lead</option>
      <option value="ativo">Ativo</option>
      <option value="inativo">Inativo</option>
    </select>
    @if(auth()->user()->isAdmin())
    <select wire:model.live="assignedTo" style="background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
      <option value="">Todos os responsáveis</option>
      @foreach($staff as $member)
        <option value="{{ $member->id }}">{{ $member->name }}</option>
      @endforeach
    </select>
    @endif
    <label style="display:flex;align-items:center;gap:6px;font-size:.82rem;color:#697386;cursor:pointer;white-space:nowrap">
      <input type="checkbox" wire:model.live="showArchived" style="width:auto">
      Mostrar arquivados
    </label>
  </div>

  @if(!empty($selected))
  <div style="display:flex;gap:10px;align-items:center;background:#EEF2FF;border:1px solid #C7D2FE;border-radius:8px;padding:10px 16px;margin-bottom:16px;flex-wrap:wrap">
    <span style="font-size:.82rem;font-weight:600;color:#3730A3">{{ count($selected) }} selecionado(s)</span>
    <select wire:model="bulkAssignedTo" style="background:#fff;border:1px solid #D1D5DB;padding:6px 10px;border-radius:6px;font-size:.8rem">
      <option value="">Mudar responsável...</option>
      @foreach($staff as $member)
        <option value="{{ $member->id }}">{{ $member->name }}</option>
      @endforeach
    </select>
    <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" wire:click="bulkAssign">Aplicar</button>

    <input type="text" wire:model="bulkTagName" placeholder="Nome da tag..." style="background:#fff;border:1px solid #D1D5DB;padding:6px 10px;border-radius:6px;font-size:.8rem;width:140px">
    <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" wire:click="bulkAddTag">+ Tag</button>

    <button type="button" class="btn-danger" style="padding:6px 12px;font-size:.78rem" wire:click="bulkArchive" onclick="return confirm('Arquivar os contatos selecionados?')">Arquivar selecionados</button>
  </div>
  @endif

  @if($contacts->isEmpty())
    <div style="text-align:center;padding:60px;color:#697386">
      <div style="font-size:2rem;margin-bottom:12px">👤</div>
      <div style="font-weight:600;color:#374151">Nenhum contato encontrado</div>
    </div>
  @else
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:32px"><input type="checkbox" wire:model.live="selectAll" style="width:auto"></th>
          <th>Nome</th>
          <th>Contato</th>
          <th>Origem</th>
          <th>Status</th>
          <th>Tags</th>
          <th>Responsável</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($contacts as $contact)
        <tr style="{{ $contact->archived_at ? 'opacity:.55' : '' }}">
          <td><input type="checkbox" wire:model.live="selected" value="{{ $contact->id }}" style="width:auto"></td>
          <td>
            <a href="{{ route('admin.crm.contacts.show', $contact) }}" style="color:#1A1F36;font-weight:600;text-decoration:none">{{ $contact->name }}</a>
            @if($contact->archived_at) <span class="badge badge-red" style="margin-left:6px">Arquivado</span> @endif
          </td>
          <td style="color:#697386;font-size:.85rem">{{ $contact->email ?: '—' }}<br>{{ $contact->phone ?: '' }}</td>
          <td style="color:#697386">{{ $contact->source_label }}</td>
          <td>
            @if($contact->status === 'ativo')
              <span class="badge badge-green">Ativo</span>
            @elseif($contact->status === 'inativo')
              <span class="badge badge-red">Inativo</span>
            @else
              <span class="badge badge-blue">Lead</span>
            @endif
          </td>
          <td>
            @foreach($contact->tags as $tag)
              <span class="badge" style="background:{{ $tag->color }}22;color:{{ $tag->color }};margin-right:4px">{{ $tag->name }}</span>
            @endforeach
          </td>
          <td style="color:#697386">{{ $contact->assignee?->name ?? '—' }}</td>
          <td style="display:flex;gap:8px">
            <a href="{{ route('admin.crm.contacts.show', $contact) }}" class="btn-secondary" style="padding:6px 12px;font-size:.78rem">Ver</a>
            <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem;white-space:nowrap" wire:click="verNegociacoes({{ $contact->id }})">Ver Negociações</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div style="margin-top:18px">{{ $contacts->links() }}</div>
  @endif

  @if($showCreateModal)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showCreateModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:480px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:18px;color:#1A1F36">Novo contato</h2>

      @if($duplicateContact)
        <div class="alert-error" style="display:flex;justify-content:space-between;align-items:center;gap:12px">
          <span>Já existe um contato com esse e-mail ou telefone: <strong>{{ $duplicateContact->name }}</strong>.</span>
          <a href="{{ route('admin.crm.contacts.show', $duplicateContact) }}" class="btn-secondary" style="padding:5px 10px;font-size:.75rem;white-space:nowrap">Ver contato</a>
        </div>
      @endif

      <form wire:submit="createContact">
        <div class="form-group">
          <label>Nome *</label>
          <input type="text" wire:model="name">
          @error('name') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>E-mail</label>
            <input type="email" wire:model="email">
            @error('email') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
          </div>
          <div class="form-group">
            <label>Telefone</label>
            <input type="text" wire:model="phone">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Origem</label>
            <select wire:model="source">
              <option value="manual">Manual</option>
              <option value="site">Site</option>
              <option value="instagram">Instagram</option>
              <option value="whatsapp">WhatsApp</option>
              <option value="email">E-mail</option>
              <option value="indicacao">Indicação</option>
              <option value="outro">Outro</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select wire:model="newStatus">
              <option value="lead">Lead</option>
              <option value="ativo">Ativo</option>
              <option value="inativo">Inativo</option>
            </select>
          </div>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="form-group">
          <label>Responsável</label>
          <select wire:model="newAssignedTo">
            <option value="">Sem responsável</option>
            @foreach($staff as $member)
              <option value="{{ $member->id }}">{{ $member->name }}</option>
            @endforeach
          </select>
        </div>
        @endif
        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" class="btn-primary">Criar contato</button>
          <button type="button" class="btn-secondary" wire:click="$set('showCreateModal', false)">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  @if($showDealsModal && $viewingContact)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showDealsModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:520px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:4px;color:#1A1F36">Negociações de {{ $viewingContact->name }}</h2>
      <div style="color:#697386;font-size:.82rem;margin-bottom:18px">{{ $viewingContact->email ?: $viewingContact->phone ?: '—' }}</div>

      @if($viewingContact->deals->isEmpty())
        <div style="color:#697386;font-size:.85rem;margin-bottom:18px">Nenhuma negociação para este cliente.</div>
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
