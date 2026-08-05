<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmDeal;
use Livewire\Attributes\Url;
use Livewire\Component;

class ConversationShow extends Component
{
    public CrmDeal $deal;

    #[Url]
    public string $channel = '';

    public function mount(CrmDeal $deal)
    {
        abort_if(auth()->user()->isVendedor() && $deal->assigned_to !== auth()->id(), 403);

        $this->deal = $deal;
    }

    public function render()
    {
        $this->deal->load('contact');

        $query = $this->deal->channelMessages()->with('author');

        if ($this->channel !== '') {
            $query->where('channel', $this->channel);
        }

        $messages = $query->get()->groupBy(fn ($m) => $m->occurred_at->format('Y-m-d'));

        $availableChannels = $this->deal->channelMessages()->reorder()->distinct()->pluck('channel');

        return view('livewire.admin.crm.conversation-show', [
            'messages'          => $messages,
            'availableChannels' => $availableChannels,
        ])->extends('layouts.admin', ['title' => 'Conversa | CRM']);
    }
}
