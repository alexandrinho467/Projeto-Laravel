<div style="position:relative;width:100%" x-data="{ open: false }" x-on:click.outside="open = false">
  <input
    type="text"
    wire:model.live.debounce.300ms="q"
    x-on:focus="open = true"
    placeholder="Buscar contato, pedido..."
    style="width:100%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:#fff;padding:7px 12px;border-radius:6px;font-size:.82rem;outline:none"
  >

  @if(strlen(trim($q)) >= 2 || (trim($q) !== '' && ctype_digit(trim($q))))
    <div x-show="open" style="position:absolute;top:calc(100% + 6px);left:0;width:300px;background:#fff;border:1px solid #E3E8EE;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:100;max-height:360px;overflow-y:auto">
      @if($contacts->isEmpty() && $orders->isEmpty())
        <div style="padding:16px;color:#697386;font-size:.82rem">Nenhum resultado para "{{ $q }}"</div>
      @else
        @if($contacts->isNotEmpty())
          <div style="padding:8px 14px 4px;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF">Contatos</div>
          @foreach($contacts as $contact)
            <a href="{{ route('admin.crm.contacts.show', $contact) }}" style="display:block;padding:8px 14px;text-decoration:none;color:#1A1F36;font-size:.85rem;border-top:1px solid #F1F5F9">
              <div style="font-weight:600">{{ $contact->name }}</div>
              <div style="color:#697386;font-size:.75rem">{{ $contact->email }} {{ $contact->phone ? '· '.$contact->phone : '' }}</div>
            </a>
          @endforeach
        @endif

        @if($orders->isNotEmpty())
          <div style="padding:8px 14px 4px;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF">Pedidos</div>
          @foreach($orders as $order)
            <a href="{{ route('admin.orders.show', $order) }}" style="display:block;padding:8px 14px;text-decoration:none;color:#1A1F36;font-size:.85rem;border-top:1px solid #F1F5F9">
              <div style="font-weight:600">Pedido #{{ $order->id }}</div>
              <div style="color:#697386;font-size:.75rem">{{ $order->guest_name }} · {{ $order->total_formatted }}</div>
            </a>
          @endforeach
        @endif
      @endif
    </div>
  @endif
</div>
