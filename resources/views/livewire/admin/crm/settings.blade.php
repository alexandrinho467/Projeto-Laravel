<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Configurações do CRM</h1>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="stat-card" style="max-width:420px;margin-bottom:24px">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px;color:#1A1F36">E-mail diário de tarefas atrasadas</h2>
    <form wire:submit="saveEmailHour">
      <div class="form-group">
        <label>Horário mínimo de envio (0–23h)</label>
        <input type="number" min="0" max="23" wire:model="emailHour">
        @error('emailHour') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        <div style="color:#697386;font-size:.78rem;margin-top:6px">O sistema envia no máximo 1 e-mail por dia, a partir desse horário.</div>
      </div>
      <button type="submit" class="btn-primary">Salvar</button>
    </form>
  </div>

  <div class="stat-card" style="max-width:600px">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px;color:#1A1F36">Checklist por estágio</h2>

    <div class="form-group">
      <label>Estágio</label>
      <select wire:model.live="checklistStage">
        @foreach($stages as $stage)
          <option value="{{ $stage }}">{{ \App\Models\CrmDeal::make(['stage' => $stage])->stage_label }}</option>
        @endforeach
      </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
      @forelse($checklistItems as $item)
        <div style="display:flex;justify-content:space-between;align-items:center;background:#F6F9FC;border:1px solid #E3E8EE;border-radius:6px;padding:8px 12px">
          <span style="font-size:.85rem">{{ $item->label }}</span>
          <button type="button" class="btn-danger" style="padding:4px 10px;font-size:.72rem" wire:click="removeChecklistItem({{ $item->id }})">Remover</button>
        </div>
      @empty
        <div style="color:#697386;font-size:.85rem">Nenhum item de checklist para este estágio.</div>
      @endforelse
    </div>

    <form wire:submit="addChecklistItem" style="display:flex;gap:10px">
      <input type="text" wire:model="checklistLabel" placeholder="Novo item, ex: Confirmar cor" style="flex:1;background:#fff;border:1px solid #D1D5DB;padding:8px 12px;border-radius:6px;font-size:.85rem">
      <button type="submit" class="btn-secondary">+ Adicionar</button>
    </form>
    @error('checklistLabel') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
  </div>

  <div class="stat-card" style="max-width:420px;margin-top:24px">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:8px;color:#1A1F36">Sincronização retroativa</h2>
    <p style="color:#697386;font-size:.85rem;margin-bottom:14px">Traz pedidos antigos da loja que ainda não têm contato/negócio no CRM (útil pra popular LTV e relatórios com o histórico real de vendas).</p>
    <button type="button" class="btn-secondary" wire:click="syncHistoricalOrders" onclick="return confirm('Sincronizar todos os pedidos ainda não vinculados ao CRM?')">Sincronizar Pedidos Históricos</button>
  </div>
</div>
