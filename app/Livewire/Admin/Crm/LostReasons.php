<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmLostReason;
use Livewire\Component;

class LostReasons extends Component
{
    public string $name = '';
    public bool $showCreateModal = false;

    public function create()
    {
        $data = $this->validate(['name' => 'required|string|max:255|unique:crm_lost_reasons,name']);

        CrmLostReason::create(['name' => $data['name'], 'active' => true]);

        $this->reset(['name', 'showCreateModal']);
    }

    public function toggleActive($id)
    {
        $reason = CrmLostReason::findOrFail($id);
        $reason->update(['active' => !$reason->active]);
    }

    public function delete($id)
    {
        CrmLostReason::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.crm.lost-reasons', [
            'reasons' => CrmLostReason::orderBy('name')->get(),
        ])->extends('layouts.admin', ['title' => 'Motivos de Perda | CRM']);
    }
}
