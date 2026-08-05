<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmLostReason;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;

class Pipeline extends Component
{
    #[Url]
    public string $assignedTo = '';

    #[Url]
    public ?int $openDeal = null;

    public bool $showDealModal = false;
    public ?int $editingDealId = null;
    public string $dealTitle = '';
    public string $dealValue = '';
    public string $dealContactId = '';
    public string $dealAssignedTo = '';

    public bool $showLossModal = false;
    public ?int $lossDealId = null;
    public array $lossOrderedIds = [];
    public string $lossReasonId = '';
    public string $lossReasonOther = '';

    public function mount()
    {
        if (auth()->user()->isVendedor()) {
            $this->assignedTo = (string) auth()->id();
        }

        if ($this->openDeal) {
            $this->openEditDeal($this->openDeal);
            $this->openDeal = null;
        }
    }

    public function updateStage($dealId, $newStage, $orderedIds = [])
    {
        if (!in_array($newStage, CrmDeal::STAGES, true)) {
            return;
        }

        $deal = CrmDeal::findOrFail($dealId);

        abort_if(auth()->user()->isVendedor() && $deal->assigned_to !== auth()->id(), 403);

        $this->applyStageChange($deal, $newStage);

        foreach ($orderedIds as $index => $id) {
            CrmDeal::where('id', $id)->update(['position' => $index]);
        }
    }

    protected function applyStageChange(CrmDeal $deal, string $newStage, ?string $lostReason = null): void
    {
        if ($deal->stage !== $newStage) {
            $oldLabel = $deal->stage_label;
            $deal->stage = $newStage;
            $deal->stage_changed_at = now();
            if ($newStage === 'perdido' && $lostReason) {
                $deal->lost_reason = $lostReason;
            }
            $deal->save();

            $description = "Estágio alterado de {$oldLabel} para {$deal->stage_label}.";
            if ($newStage === 'perdido' && $lostReason) {
                $description .= " Motivo: {$lostReason}.";
            }

            CrmActivity::create([
                'crm_contact_id' => $deal->crm_contact_id,
                'crm_deal_id'    => $deal->id,
                'user_id'        => auth()->id(),
                'type'           => 'nota',
                'description'    => $description,
            ]);
        }
    }

    public function openLossModal($dealId, $orderedIds = [])
    {
        $deal = CrmDeal::findOrFail($dealId);
        abort_if(auth()->user()->isVendedor() && $deal->assigned_to !== auth()->id(), 403);

        $this->lossDealId = (int) $dealId;
        $this->lossOrderedIds = $orderedIds;
        $this->lossReasonId = '';
        $this->lossReasonOther = '';
        $this->showLossModal = true;
    }

    public function confirmLoss()
    {
        $data = $this->validate([
            'lossReasonId'    => 'nullable|exists:crm_lost_reasons,id',
            'lossReasonOther' => 'nullable|string|max:255',
        ]);

        if (empty($data['lossReasonId']) && empty($data['lossReasonOther'])) {
            $this->addError('lossReasonId', 'Escolha um motivo ou descreva no campo "Outro".');
            return;
        }

        $reason = !empty($data['lossReasonId'])
            ? CrmLostReason::find($data['lossReasonId'])->name
            : $data['lossReasonOther'];

        $deal = CrmDeal::findOrFail($this->lossDealId);
        abort_if(auth()->user()->isVendedor() && $deal->assigned_to !== auth()->id(), 403);

        $this->applyStageChange($deal, 'perdido', $reason);

        foreach ($this->lossOrderedIds as $index => $id) {
            CrmDeal::where('id', $id)->update(['position' => $index]);
        }

        $this->reset(['showLossModal', 'lossDealId', 'lossOrderedIds', 'lossReasonId', 'lossReasonOther']);
    }

    public function cancelLossModal()
    {
        $this->reset(['showLossModal', 'lossDealId', 'lossOrderedIds', 'lossReasonId', 'lossReasonOther']);
    }

    public function openCreateDeal()
    {
        $this->reset(['editingDealId', 'dealTitle', 'dealValue', 'dealContactId', 'dealAssignedTo']);
        if (auth()->user()->isVendedor()) {
            $this->dealAssignedTo = (string) auth()->id();
        }
        $this->showDealModal = true;
    }

    public function toggleChecklistItem($dealId, $key)
    {
        $deal = CrmDeal::findOrFail($dealId);
        abort_if(auth()->user()->isVendedor() && $deal->assigned_to !== auth()->id(), 403);

        if (!array_key_exists($key, $deal->checklistItems())) return;

        $state = $deal->checklist_state ?? [];
        $state[$key] = !($state[$key] ?? false);
        $deal->update(['checklist_state' => $state]);
    }

    public function openEditDeal($dealId)
    {
        $deal = CrmDeal::findOrFail($dealId);
        abort_if(auth()->user()->isVendedor() && $deal->assigned_to !== auth()->id(), 403);

        $this->editingDealId   = $deal->id;
        $this->dealTitle       = $deal->title;
        $this->dealValue       = (string) $deal->value;
        $this->dealContactId   = (string) $deal->crm_contact_id;
        $this->dealAssignedTo  = (string) ($deal->assigned_to ?? '');
        $this->showDealModal   = true;
    }

    public function saveDeal()
    {
        $data = $this->validate([
            'dealTitle'      => 'required|string|max:255',
            'dealValue'      => 'nullable|numeric|min:0',
            'dealContactId'  => 'required|exists:crm_contacts,id',
            'dealAssignedTo' => 'nullable|exists:users,id',
        ]);

        $assignedTo = auth()->user()->isVendedor() ? auth()->id() : ($data['dealAssignedTo'] ?: null);

        if ($this->editingDealId) {
            $deal = CrmDeal::findOrFail($this->editingDealId);
            abort_if(auth()->user()->isVendedor() && $deal->assigned_to !== auth()->id(), 403);

            $deal->update([
                'title'          => $data['dealTitle'],
                'value'          => $data['dealValue'] ?: 0,
                'crm_contact_id' => $data['dealContactId'],
                'assigned_to'    => $assignedTo,
            ]);
        } else {
            CrmDeal::create([
                'crm_contact_id'   => $data['dealContactId'],
                'assigned_to'      => $assignedTo,
                'title'            => $data['dealTitle'],
                'value'            => $data['dealValue'] ?: 0,
                'stage'            => 'novo_lead',
                'stage_changed_at' => now(),
            ]);
        }

        $this->reset(['editingDealId', 'dealTitle', 'dealValue', 'dealContactId', 'dealAssignedTo']);
        $this->showDealModal = false;
    }

    public function render()
    {
        $user = auth()->user();

        $query = CrmDeal::query()->with(['contact', 'assignee']);

        if ($user->isVendedor()) {
            $query->where('assigned_to', $user->id);
        } elseif ($this->assignedTo !== '') {
            $query->where('assigned_to', $this->assignedTo);
        }

        $deals = $query->orderBy('position')->get()->groupBy('stage');

        return view('livewire.admin.crm.pipeline', [
            'dealsByStage' => $deals,
            'stages'       => CrmDeal::STAGES,
            'staff'        => User::whereIn('role', ['admin', 'vendedor'])->orderBy('name')->get(),
            'contacts'     => CrmContact::orderBy('name')->get(['id', 'name']),
            'editingDeal'  => $this->editingDealId ? CrmDeal::find($this->editingDealId) : null,
            'lostReasons'  => CrmLostReason::where('active', true)->orderBy('name')->get(),
        ])->extends('layouts.admin', ['title' => 'Pipeline | CRM']);
    }
}
