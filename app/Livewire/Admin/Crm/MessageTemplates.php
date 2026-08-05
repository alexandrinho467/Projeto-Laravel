<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmMessageTemplate;
use Livewire\Component;

class MessageTemplates extends Component
{
    public bool $showFormModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $body = '';

    public function openCreate()
    {
        abort_if(auth()->user()->isVendedor(), 403);
        $this->reset(['editingId', 'name', 'body']);
        $this->showFormModal = true;
    }

    public function openEdit($id)
    {
        abort_if(auth()->user()->isVendedor(), 403);
        $template = CrmMessageTemplate::findOrFail($id);
        $this->editingId = $template->id;
        $this->name = $template->name;
        $this->body = $template->body;
        $this->showFormModal = true;
    }

    public function save()
    {
        abort_if(auth()->user()->isVendedor(), 403);

        $data = $this->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
        ]);

        CrmMessageTemplate::updateOrCreate(['id' => $this->editingId], $data);

        $this->reset(['showFormModal', 'editingId', 'name', 'body']);
    }

    public function delete($id)
    {
        abort_if(auth()->user()->isVendedor(), 403);
        CrmMessageTemplate::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.crm.message-templates', [
            'templates' => CrmMessageTemplate::orderBy('name')->get(),
        ])->extends('layouts.admin', ['title' => 'Modelos de Mensagens | CRM']);
    }
}
