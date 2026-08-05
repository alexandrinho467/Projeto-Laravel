<div>
  <div class="admin-topbar">
    <div>
      <h1 class="admin-title">Conversa — {{ $deal->contact->name }}</h1>
      <div style="color:#697386;font-size:.85rem">{{ $deal->title }} · <span class="badge badge-blue">{{ $deal->stage_label }}</span></div>
    </div>
    <a href="{{ route('admin.crm.contacts.show', $deal->contact) }}" class="btn-secondary">← Voltar ao contato</a>
  </div>

  @if($availableChannels->count() > 1)
  <div style="display:flex;gap:8px;margin-bottom:18px">
    <button type="button" class="{{ $channel === '' ? 'btn-primary' : 'btn-secondary' }}" style="padding:6px 14px;font-size:.8rem" wire:click="$set('channel', '')">Todos</button>
    @foreach($availableChannels as $canal)
      <button type="button" class="{{ $channel === $canal ? 'btn-primary' : 'btn-secondary' }}" style="padding:6px 14px;font-size:.8rem" wire:click="$set('channel', '{{ $canal }}')">{{ ['whatsapp' => 'WhatsApp', 'instagram' => 'Instagram', 'email' => 'E-mail'][$canal] ?? $canal }}</button>
    @endforeach
  </div>
  @endif

  @if($messages->isEmpty())
    <div style="text-align:center;padding:60px;color:#697386">
      <div style="font-size:2rem;margin-bottom:12px">💬</div>
      <div style="font-weight:600;color:#374151">Nenhuma mensagem registrada para esta negociação.</div>
    </div>
  @else
    <div style="background:#F7F9FC;border:1px solid #E3E8EE;border-radius:10px;padding:24px;max-width:760px">
      @foreach($messages as $day => $dayMessages)
        <div style="text-align:center;margin-bottom:16px">
          <span style="background:#E3E8EE;color:#697386;font-size:.72rem;padding:4px 10px;border-radius:20px">{{ \Illuminate\Support\Carbon::parse($day)->format('d/m/Y') }}</span>
        </div>

        @foreach($dayMessages as $message)
          <div style="display:flex;flex-direction:column;margin-bottom:14px;{{ $message->direction === 'enviada' ? 'align-items:flex-end' : 'align-items:flex-start' }}">
            <div style="max-width:70%;padding:10px 14px;border-radius:12px;{{ $message->direction === 'enviada' ? 'background:#635BFF;color:#fff;border-bottom-right-radius:2px' : 'background:#fff;color:#1A1F36;border:1px solid #E3E8EE;border-bottom-left-radius:2px' }}">
              @if($message->subject)
                <div style="font-weight:700;font-size:.82rem;margin-bottom:4px">{{ $message->subject }}</div>
              @endif
              <div style="font-size:.88rem;white-space:pre-line">{{ $message->content }}</div>
            </div>
            <div style="font-size:.7rem;color:#8792A2;margin-top:4px;display:flex;gap:6px;align-items:center">
              <span class="badge badge-blue">{{ $message->channel_label }}</span>
              <span>{{ $message->direction === 'enviada' ? ($message->author?->name ?? 'Vendedor') : $deal->contact->name }}</span>
              <span>{{ $message->occurred_at->format('H:i') }}</span>
            </div>
          </div>
        @endforeach
      @endforeach
    </div>
  @endif
</div>
