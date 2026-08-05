<div>
  <div class="admin-topbar">
    <h1 class="admin-title">
      {{ $contact->name }}
      @if($contact->archived_at) <span class="badge badge-red">Arquivado</span> @endif
    </h1>
    <div style="display:flex;gap:10px">
      <button type="button" class="btn-secondary" wire:click="toggleArchive" onclick="return confirm('{{ $contact->archived_at ? 'Desarquivar' : 'Arquivar' }} este contato?')">
        {{ $contact->archived_at ? 'Desarquivar' : 'Arquivar' }}
      </button>
      <a href="{{ route('admin.crm.contacts.index') }}" class="btn-secondary">← Contatos</a>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start">
    <div class="stat-card">
      <div class="form-group">
        <label>Nome</label>
        <input type="text" value="{{ $contact->name }}" wire:blur="updateField('name', $event.target.value)">
      </div>
      <div class="form-group">
        <label>E-mail</label>
        <input type="email" value="{{ $contact->email }}" wire:blur="updateField('email', $event.target.value)">
      </div>
      <div class="form-group">
        <label>Telefone</label>
        <input type="text" value="{{ $contact->phone }}" wire:blur="updateField('phone', $event.target.value)">
      </div>
      <div class="form-group">
        <label>Status</label>
        <select wire:change="updateField('status', $event.target.value)">
          <option value="lead" {{ $contact->status === 'lead' ? 'selected' : '' }}>Lead</option>
          <option value="ativo" {{ $contact->status === 'ativo' ? 'selected' : '' }}>Ativo</option>
          <option value="inativo" {{ $contact->status === 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
      </div>
      @if(auth()->user()->isAdmin())
      <div class="form-group">
        <label>Responsável</label>
        <select wire:change="updateField('assigned_to', $event.target.value)">
          <option value="">Sem responsável</option>
          @foreach($staff as $member)
            <option value="{{ $member->id }}" {{ $contact->assigned_to == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
          @endforeach
        </select>
      </div>
      @endif
      <div class="form-group">
        <label>Origem</label>
        <div style="color:#697386;font-size:.85rem">{{ $contact->source_label }}</div>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>Valor vitalício (LTV)</label>
        <div style="color:#1A1F36;font-weight:700;font-size:1rem">{{ $contact->ltv_formatted }}</div>
      </div>

      <div style="margin-top:14px;padding-top:14px;border-top:1px solid #E3E8EE">
        <label style="display:block;font-size:.82rem;color:#374151;margin-bottom:6px;font-weight:500">Tags</label>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
          @forelse($contact->tags as $tag)
            <span class="badge" style="background:{{ $tag->color }}22;color:{{ $tag->color }};display:inline-flex;align-items:center;gap:6px">
              {{ $tag->name }}
              <button type="button" wire:click="removeTag({{ $tag->id }})" style="border:none;background:none;color:inherit;cursor:pointer;font-weight:700;padding:0;line-height:1">×</button>
            </span>
          @empty
            <span style="color:#9CA3AF;font-size:.8rem">Nenhuma tag</span>
          @endforelse
        </div>
        <form wire:submit="addTag" style="display:flex;gap:6px">
          <input type="text" wire:model="newTagName" placeholder="Nova tag..." style="flex:1;background:#fff;border:1px solid #D1D5DB;padding:6px 10px;border-radius:6px;font-size:.8rem">
          <button type="submit" class="btn-secondary" style="padding:6px 12px;font-size:.78rem">+</button>
        </form>
      </div>

      @if($contact->user)
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid #E3E8EE">
          <a href="{{ route('admin.orders.index') }}" class="btn-secondary" style="width:100%;justify-content:center">Ver conta de cliente</a>
        </div>
      @endif
    </div>

    <div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <h2 style="font-size:1rem;font-weight:700;color:#1A1F36">Negócios</h2>
        <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" wire:click="$set('showDealModal', true)">+ Novo negócio</button>
      </div>

      @if($contact->deals->isEmpty())
        <div style="color:#697386;font-size:.85rem;margin-bottom:24px">Nenhum negócio ainda.</div>
      @else
        <table class="admin-table" style="margin-bottom:24px">
          <thead>
            <tr><th>Título</th><th>Valor</th><th>Estágio</th></tr>
          </thead>
          <tbody>
            @foreach($contact->deals as $deal)
            <tr>
              <td><a href="{{ route('admin.crm.pipeline') }}">{{ $deal->title }}</a></td>
              <td>{{ $deal->value_formatted }}</td>
              <td><span class="badge badge-blue">{{ $deal->stage_label }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif

      <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px;color:#1A1F36">Atividades</h2>

      <form wire:submit="addActivity" style="background:#fff;border:1px solid #E3E8EE;border-radius:8px;padding:16px;margin-bottom:18px">
        <div class="form-row">
          <div class="form-group">
            <label>Tipo</label>
            <select wire:model="activityType">
              <option value="nota">Nota</option>
              <option value="ligacao">Ligação</option>
              <option value="email">E-mail</option>
              <option value="whatsapp">WhatsApp</option>
              <option value="reuniao">Reunião</option>
              <option value="tarefa">Tarefa</option>
            </select>
          </div>
          <div class="form-group">
            <label>Prazo (para tarefas)</label>
            <input type="date" wire:model="activityDueDate">
          </div>
        </div>
        <div class="form-group">
          <label>Descrição *</label>
          <textarea wire:model="activityDescription" rows="2"></textarea>
          @error('activityDescription') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn-primary">Adicionar</button>
      </form>

      <div style="display:flex;flex-direction:column;gap:10px">
        @forelse($contact->activities as $activity)
          <div style="background:#fff;border:1px solid #E3E8EE;border-radius:8px;padding:12px 16px">
            <div style="display:flex;justify-content:space-between;align-items:start">
              <div>
                <span class="badge badge-blue">{{ $activity->type_label }}</span>
                <span style="color:#697386;font-size:.78rem;margin-left:8px">{{ $activity->author?->name ?? 'Sistema' }} · {{ $activity->created_at->format('d/m/Y H:i') }}</span>
              </div>
              @if($activity->due_date && !$activity->completed_at)
                <button type="button" class="btn-secondary" style="padding:4px 10px;font-size:.72rem" wire:click="completeActivity({{ $activity->id }})">Concluir</button>
              @endif
            </div>
            <div style="margin-top:8px;font-size:.87rem;color:#1A1F36">{{ $activity->description }}</div>
            @if($activity->due_date)
              <div style="margin-top:8px">
                @if($activity->completed_at)
                  <span class="badge badge-green">Concluída em {{ $activity->completed_at->format('d/m/Y') }}</span>
                @elseif($activity->is_overdue)
                  <span class="badge badge-red">Atrasada — prazo {{ $activity->due_date->format('d/m/Y') }}</span>
                @else
                  <span class="badge badge-yellow">Prazo {{ $activity->due_date->format('d/m/Y') }}</span>
                @endif
              </div>
            @endif
          </div>
        @empty
          <div style="color:#697386;font-size:.85rem">Nenhuma atividade registrada ainda.</div>
        @endforelse
      </div>

      @if($messageTemplates->isNotEmpty())
        <h2 style="font-size:1rem;font-weight:700;margin:24px 0 12px;color:#1A1F36">Mensagens rápidas</h2>
        <div style="display:flex;flex-direction:column;gap:10px">
          @foreach($messageTemplates as $tpl)
            <div style="background:#fff;border:1px solid #E3E8EE;border-radius:8px;padding:12px 16px">
              <div style="font-weight:600;color:#1A1F36;margin-bottom:6px">{{ $tpl->name }}</div>
              <div style="color:#697386;font-size:.83rem;white-space:pre-wrap;margin-bottom:10px" id="msg-rendered-{{ $tpl->id }}">{{ $tpl->rendered }}</div>
              <div style="display:flex;gap:8px">
                <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" onclick="navigator.clipboard.writeText(document.getElementById('msg-rendered-{{ $tpl->id }}').innerText); this.innerText='Copiado!'; setTimeout(() => this.innerText='Copiar', 1500)">Copiar</button>
                @if($tpl->whatsappUrl)
                  <a href="{{ $tpl->whatsappUrl }}" target="_blank" class="btn-primary" style="padding:6px 12px;font-size:.78rem">Enviar via WhatsApp</a>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  @if($showDealModal)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showDealModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:420px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:18px;color:#1A1F36">Novo negócio</h2>
      <form wire:submit="createDeal">
        <div class="form-group">
          <label>Título *</label>
          <input type="text" wire:model="dealTitle">
          @error('dealTitle') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label>Valor estimado (AED)</label>
          <input type="number" step="0.01" min="0" wire:model="dealValue">
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" class="btn-primary">Criar negócio</button>
          <button type="button" class="btn-secondary" wire:click="$set('showDealModal', false)">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endif
</div>
