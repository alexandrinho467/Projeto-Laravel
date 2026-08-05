<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Meu E-mail</h1>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="stat-card" style="max-width:480px">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:4px;color:#1A1F36">
      Sincronização de e-mail (IMAP)
      @if($hasStoredPassword)
        <span class="badge badge-green" style="margin-left:4px">Conectado</span>
      @else
        <span class="badge badge-yellow" style="margin-left:4px">Não configurado</span>
      @endif
    </h2>
    <p style="color:#697386;font-size:.83rem;margin-bottom:18px">Conecte sua caixa de e-mail para que as mensagens trocadas com clientes apareçam automaticamente no histórico do CRM.</p>

    <form wire:submit="save">
      <div class="form-group">
        <label>Servidor IMAP</label>
        <input type="text" wire:model="imapHost" placeholder="imap.gmail.com">
        @error('imapHost') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Porta</label>
          <input type="number" wire:model="imapPort" placeholder="993">
          @error('imapPort') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
          <label>Criptografia</label>
          <select wire:model="imapEncryption">
            <option value="ssl">SSL</option>
            <option value="tls">TLS</option>
            <option value="none">Nenhuma</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Usuário / E-mail</label>
        <input type="text" wire:model="imapUsername" placeholder="seuemail@gmail.com">
        @error('imapUsername') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
      </div>
      <div class="form-group">
        <label>Senha{{ $hasStoredPassword ? ' (deixe em branco para manter a atual)' : '' }}</label>
        <input type="password" wire:model="imapPassword" placeholder="{{ $hasStoredPassword ? '••••••••' : '' }}" autocomplete="new-password">
        @error('imapPassword') <div style="color:#B91C1C;font-size:.78rem;margin-top:4px">{{ $message }}</div> @enderror
        <div style="color:#697386;font-size:.78rem;margin-top:6px">Para provedores como Gmail, use uma "senha de app", não a senha normal da conta.</div>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>Pasta</label>
        <input type="text" wire:model="imapFolder" placeholder="INBOX">
      </div>

      @if($testResult !== null)
        <div class="{{ $testResult ? 'alert-success' : 'alert-error' }}" style="margin-top:16px">{{ $testMessage }}</div>
      @endif

      <div style="display:flex;gap:12px;margin-top:18px;flex-wrap:wrap">
        <button type="submit" class="btn-primary">Salvar</button>
        <button type="button" class="btn-secondary" wire:click="testConnection">Testar conexão</button>
        @if($hasStoredPassword)
          <button type="button" class="btn-danger" wire:click="disconnect" onclick="return confirm('Desconectar sua caixa de e-mail do CRM?')">Desconectar</button>
        @endif
      </div>
    </form>
  </div>
</div>
