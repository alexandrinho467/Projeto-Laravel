<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmChannelMessage;
use App\Models\CrmContact;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LogsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $channel = '';

    #[Url]
    public string $vendedorId = '';

    public bool $showDealsModal = false;
    public ?CrmContact $viewingContact = null;
    public ?User $dealsVendedor = null;

    public bool $showHistoryModal = false;
    public ?CrmContact $historyContact = null;
    public ?User $historyVendedor = null;
    public $historyMessages = [];

    public function mount()
    {
        if (auth()->user()->isVendedor()) {
            $this->vendedorId = (string) auth()->id();
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingChannel() { $this->resetPage(); }
    public function updatingVendedorId() { $this->resetPage(); }

    public function verNegociacoes($contactId, $vendedorId = null)
    {
        if (auth()->user()->isVendedor()) {
            $vendedorId = auth()->id();
        }

        $this->viewingContact = CrmContact::with(['deals' => function ($q) use ($vendedorId) {
            $q->latest();
            if ($vendedorId) {
                $q->where('assigned_to', $vendedorId);
            }
        }])->find($contactId);

        $this->dealsVendedor = $vendedorId ? User::find($vendedorId) : null;
        $this->showDealsModal = true;
    }

    public function verHistorico($contactId, $vendedorId, $channel)
    {
        $this->historyContact  = CrmContact::find($contactId);
        $this->historyVendedor = $vendedorId ? User::find($vendedorId) : null;

        $query = CrmChannelMessage::where('crm_contact_id', $contactId)->where('channel', $channel);
        $vendedorId ? $query->where('user_id', $vendedorId) : $query->whereNull('user_id');

        $this->historyMessages = $query->orderBy('occurred_at')->get();
        $this->showHistoryModal = true;
    }

    public function render()
    {
        $user = auth()->user();

        $base = CrmChannelMessage::query();

        if ($user->isVendedor()) {
            $base->where('user_id', $user->id);
        } elseif ($this->vendedorId !== '') {
            $base->where('user_id', $this->vendedorId);
        }

        if ($this->channel !== '') {
            $base->where('channel', $this->channel);
        }

        if ($this->search !== '') {
            $base->whereHas('contact', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        $grouped = (clone $base)
            ->selectRaw('crm_contact_id, user_id, channel, COUNT(*) as total, MIN(occurred_at) as primeira_interacao, MAX(occurred_at) as ultima_interacao')
            ->groupBy('crm_contact_id', 'user_id', 'channel')
            ->orderByDesc('ultima_interacao')
            ->paginate(20);

        $contacts   = CrmContact::whereIn('id', $grouped->pluck('crm_contact_id'))->get()->keyBy('id');
        $vendedores = User::whereIn('id', $grouped->pluck('user_id')->filter())->get()->keyBy('id');

        $channelScope = CrmChannelMessage::query();
        if ($user->isVendedor()) {
            $channelScope->where('user_id', $user->id);
        }
        $availableChannels = $channelScope->distinct()->pluck('channel');

        return view('livewire.admin.crm.logs-index', [
            'logs'              => $grouped,
            'contacts'          => $contacts,
            'vendedores'        => $vendedores,
            'availableChannels' => $availableChannels,
            'staff'             => User::whereIn('role', ['admin', 'vendedor'])->orderBy('name')->get(),
        ])->extends('layouts.admin', ['title' => 'Logs Omnichannel | CRM']);
    }
}
