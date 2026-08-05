<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Motivos de Perda</h1>
    <button type="button" class="btn-primary" wire:click="$set('showCreateModal', true)">+ Novo Motivo</button>
  </div>

  <table class="admin-table">
    <thead>
      <tr><th>Nome</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      @foreach($reasons as $reason)
      <tr>
        <td>{{ $reason->name }}</td>
        <td>
          @if($reason->active)
            <span class="badge badge-green">Ativo</span>
          @else
            <span class="badge badge-red">Inativo</span>
          @endif
        </td>
        <td>
          <div style="display:flex;gap:8px">
            <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" wire:click="toggleActive({{ $reason->id }})">
              {{ $reason->active ? 'Desativar' : 'Ativar' }}
            </button>
            <button type="button" class="btn-danger" style="padding:6px 12px;font-size:.78rem" wire:click="delete({{ $reason->id }})" onclick="return confirm('Excluir o motivo {{ $reason->name }}?')">Excluir</button>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @if($showCreateModal)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showCreateModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:380px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:18px;color:#1A1F36">Novo motivo</h2>
      <form wire:submit="create">
        <div class="form-group">
          <label>Nome *</label>
          <input type="text" wire:model="name">
          @error('name') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" class="btn-primary">Criar</button>
          <button type="button" class="btn-secondary" wire:click="$set('showCreateModal', false)">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endif
</div>
