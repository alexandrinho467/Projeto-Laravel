<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Modelos de Mensagens</h1>
    @if(auth()->user()->isAdmin())
      <button type="button" class="btn-primary" wire:click="openCreate">+ Novo Modelo</button>
    @endif
  </div>

  @if($templates->isEmpty())
    <div style="text-align:center;padding:60px;color:#697386">
      <div style="font-size:2rem;margin-bottom:12px">💬</div>
      <div style="font-weight:600;color:#374151">Nenhum modelo cadastrado ainda</div>
    </div>
  @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      @foreach($templates as $template)
        <div class="stat-card" style="margin-bottom:0">
          <div style="font-weight:700;color:#1A1F36;margin-bottom:8px">{{ $template->name }}</div>
          <div style="color:#697386;font-size:.85rem;white-space:pre-wrap;margin-bottom:14px" id="tpl-body-{{ $template->id }}">{{ $template->body }}</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" onclick="navigator.clipboard.writeText(document.getElementById('tpl-body-{{ $template->id }}').innerText); this.innerText='Copiado!'; setTimeout(() => this.innerText='Copiar', 1500)">Copiar</button>
            @if(auth()->user()->isAdmin())
              <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" wire:click="openEdit({{ $template->id }})">Editar</button>
              <button type="button" class="btn-danger" style="padding:6px 12px;font-size:.78rem" wire:click="delete({{ $template->id }})" onclick="return confirm('Excluir o modelo {{ $template->name }}?')">Excluir</button>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif

  @if($showFormModal)
  <div style="position:fixed;inset:0;background:rgba(10,37,64,.45);display:flex;align-items:center;justify-content:center;z-index:50" wire:click.self="$set('showFormModal', false)">
    <div style="background:#fff;border-radius:10px;padding:28px;width:100%;max-width:480px">
      <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:18px;color:#1A1F36">{{ $editingId ? 'Editar modelo' : 'Novo modelo' }}</h2>
      <form wire:submit="save">
        <div class="form-group">
          <label>Nome *</label>
          <input type="text" wire:model="name">
          @error('name') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label>Mensagem * <span style="color:#9CA3AF;font-weight:400">(use @{{cliente}}, @{{sneaker}}, @{{valor}})</span></label>
          <textarea wire:model="body" rows="5"></textarea>
          @error('body') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" class="btn-primary">Salvar</button>
          <button type="button" class="btn-secondary" wire:click="$set('showFormModal', false)">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  @endif
</div>
