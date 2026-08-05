<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmDeal;
use App\Models\CrmStageChecklistItem;
use App\Models\Order;
use App\Models\SiteSetting;
use Livewire\Component;

class Settings extends Component
{
    public string $emailHour;

    public string $checklistStage = 'proposta';
    public string $checklistLabel = '';

    public function mount()
    {
        $this->emailHour = SiteSetting::get('crm_email_hour', '8');
    }

    public function saveEmailHour()
    {
        $data = $this->validate(['emailHour' => 'required|integer|min:0|max:23']);
        SiteSetting::set('crm_email_hour', (string) $data['emailHour']);
        session()->flash('success', 'Horário salvo!');
    }

    public function addChecklistItem()
    {
        $data = $this->validate([
            'checklistStage' => 'required|in:' . implode(',', CrmDeal::STAGES),
            'checklistLabel' => 'required|string|max:255',
        ]);

        $key = \Illuminate\Support\Str::slug($data['checklistLabel'], '_');
        $nextPosition = CrmStageChecklistItem::where('stage', $data['checklistStage'])->max('position') + 1;

        CrmStageChecklistItem::updateOrCreate(
            ['stage' => $data['checklistStage'], 'key' => $key],
            ['label' => $data['checklistLabel'], 'position' => $nextPosition]
        );

        $this->checklistLabel = '';
    }

    public function removeChecklistItem($id)
    {
        CrmStageChecklistItem::findOrFail($id)->delete();
    }

    public function syncHistoricalOrders()
    {
        $synced = Order::syncHistoricalOrders();

        session()->flash('success', $synced > 0
            ? "{$synced} pedido(s) sincronizado(s) com o CRM!"
            : 'Nenhum pedido pendente de sincronização — tudo já está no CRM.');
    }

    public function render()
    {
        return view('livewire.admin.crm.settings', [
            'stages'          => CrmDeal::STAGES,
            'checklistItems'  => CrmStageChecklistItem::where('stage', $this->checklistStage)->orderBy('position')->get(),
        ])->extends('layouts.admin', ['title' => 'Configurações do CRM | CRM']);
    }
}
