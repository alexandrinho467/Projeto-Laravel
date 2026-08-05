<div wire:poll.30s>
  @assets
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
  @endassets

  <div class="admin-topbar">
    <h1 class="admin-title">Pipeline</h1>
    <div style="display:flex;gap:10px">
      @if(auth()->user()->isAdmin())
      <select wire:model.live="assignedTo" style="background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
        <option value="">Todos os responsáveis</option>
        @foreach($staff as $member)
          <option value="{{ $member->id }}">{{ $member->name }}</option>
        @endforeach
      </select>
      @endif
      <button type="button" class="btn-primary" wire:click="openCreateDeal">+ Novo Negócio</button>
    </div>
  </div>

  <div class="kanban-board">
    @foreach($stages as $stage)
      <div class="kanban-column" wire:key="col-{{ $stage }}">
        <div class="kanban-column-header">
          <span>{{ \App\Models\CrmDeal::make(['stage' => $stage])->stage_label }}</span>
          <span>{{ ($dealsByStage[$stage] ?? collect())->count() }}</span>
        </div>
        <div class="kanban-cards" data-stage="{{ $stage }}">
          @foreach($dealsByStage[$stage] ?? [] as $deal)
            @php
              $isNewLead = $deal->stage === 'novo_lead' && $deal->created_at->diffInMinutes(now()) < 5;
              $isStalled = $deal->stage === 'proposta' && $deal->stage_changed_at->diffInDays(now()) >= 3;
              [$checkDone, $checkTotal] = $deal->checklistProgress();
            @endphp
            <div class="kanban-card @if($isNewLead) is-new-lead @elseif($isStalled) is-stalled @endif" wire:key="deal-{{ $deal->id }}" data-deal-id="{{ $deal->id }}" wire:click="openEditDeal({{ $deal->id }})">
              @if($isNewLead)
                <div class="kanban-badge kanban-badge-new">🔴 Contatar em até 5 min</div>
              @elseif($isStalled)
                <div class="kanban-badge kanban-badge-stalled">⚠ Parado há {{ $deal->stage_changed_at->diffInDays(now()) }}d</div>
              @endif
              <div style="font-weight:600;color:#1A1F36;margin-bottom:4px">{{ $deal->title }}</div>
              <div style="color:#697386;font-size:.8rem;margin-bottom:6px">{{ $deal->contact->name }}</div>
              <div class="value">{{ $deal->value_formatted }}</div>
              @if($checkTotal > 0)
                <div style="margin-top:6px;color:#635BFF;font-size:.72rem;font-weight:600">☑ {{ $checkDone }}/{{ $checkTotal }}</div>
              @endif
              @if($deal->assignee)
                <div style="margin-top:6px;color:#9CA3AF;font-size:.72rem">{{ $deal->assignee->name }}</div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  @if($showDealModal)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showDealModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:420px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:18px;color:#1A1F36">{{ $editingDealId ? 'Editar negócio' : 'Novo negócio' }}</h2>
      <form wire:submit="saveDeal">
        <div class="form-group">
          <label>Título *</label>
          <input type="text" wire:model="dealTitle">
          @error('dealTitle') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label>Contato *</label>
          <select wire:model="dealContactId">
            <option value="">Selecione...</option>
            @foreach($contacts as $contact)
              <option value="{{ $contact->id }}">{{ $contact->name }}</option>
            @endforeach
          </select>
          @error('dealContactId') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label>Valor estimado (AED)</label>
          <input type="number" step="0.01" min="0" wire:model="dealValue">
        </div>
        @if(auth()->user()->isAdmin())
        <div class="form-group">
          <label>Responsável</label>
          <select wire:model="dealAssignedTo">
            <option value="">Sem responsável</option>
            @foreach($staff as $member)
              <option value="{{ $member->id }}">{{ $member->name }}</option>
            @endforeach
          </select>
        </div>
        @endif

        @if($editingDeal && !empty($editingDeal->checklistItems()))
          <div class="form-group">
            <label>Checklist do estágio</label>
            <div style="display:flex;flex-direction:column;gap:8px;background:#F6F9FC;border:1px solid #E3E8EE;border-radius:8px;padding:12px">
              @foreach($editingDeal->checklistItems() as $key => $label)
                <label style="display:flex;align-items:center;gap:8px;font-size:.85rem;font-weight:400;cursor:pointer;margin:0">
                  <input type="checkbox" style="width:auto"
                    {{ !empty($editingDeal->checklist_state[$key] ?? null) ? 'checked' : '' }}
                    wire:click="toggleChecklistItem({{ $editingDeal->id }}, '{{ $key }}')">
                  {{ $label }}
                </label>
              @endforeach
            </div>
          </div>
        @endif

        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" class="btn-primary">{{ $editingDealId ? 'Salvar' : 'Criar negócio' }}</button>
          <button type="button" class="btn-secondary" wire:click="$set('showDealModal', false)">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  @if($showLossModal)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:60" wire:click.self="cancelLossModal">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:420px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:18px;color:#1A1F36">Motivo da perda</h2>
      <form wire:submit="confirmLoss">
        <div class="form-group">
          <label>Motivo</label>
          <select wire:model="lossReasonId">
            <option value="">Selecione...</option>
            @foreach($lostReasons as $reason)
              <option value="{{ $reason->id }}">{{ $reason->name }}</option>
            @endforeach
          </select>
          @error('lossReasonId') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label>Ou descreva (Outro)</label>
          <input type="text" wire:model="lossReasonOther" placeholder="Ex: cliente desistiu">
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" class="btn-primary">Confirmar</button>
          <button type="button" class="btn-secondary" wire:click="cancelLossModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  @script
  <script>
    $wire.$el.querySelectorAll('.kanban-cards').forEach(function (column) {
      new Sortable(column, {
        group: 'kanban',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function (evt) {
          const stage = evt.to.dataset.stage
          const orderedIds = Array.from(evt.to.children).map(function (el) { return el.dataset.dealId })
          if (stage === 'perdido') {
            $wire.openLossModal(evt.item.dataset.dealId, orderedIds)
          } else {
            $wire.updateStage(evt.item.dataset.dealId, stage, orderedIds)
          }
        }
      })
    })
  </script>
  @endscript
</div>

@push('styles')
<style>
  .kanban-board{display:flex;gap:14px;overflow-x:auto;padding-bottom:12px}
  .kanban-column{background:#F6F9FC;border-radius:8px;min-width:260px;flex-shrink:0;display:flex;flex-direction:column;max-height:calc(100vh - 220px)}
  .kanban-column-header{padding:12px 14px;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#697386;border-bottom:1px solid #E3E8EE;display:flex;justify-content:space-between}
  .kanban-cards{padding:10px;overflow-y:auto;flex:1;min-height:60px}
  .kanban-card{background:#fff;border:1px solid #E3E8EE;border-radius:8px;padding:12px;margin-bottom:8px;cursor:grab;font-size:.84rem}
  .kanban-card.sortable-ghost{opacity:.4}
  .kanban-card .value{color:#635BFF;font-weight:700}
  .kanban-card.is-new-lead{border-color:#EF4444;animation:kanban-blink 1.2s ease-in-out infinite}
  .kanban-card.is-stalled{border-color:#F59E0B;background:#FFFBEB}
  .kanban-badge{font-size:.68rem;font-weight:700;margin-bottom:6px;display:inline-block}
  .kanban-badge-new{color:#B91C1C}
  .kanban-badge-stalled{color:#92400E}
  @keyframes kanban-blink{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.5)}50%{box-shadow:0 0 0 4px rgba(239,68,68,.15)}}
</style>
@endpush
