<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmMessageTemplate;
use App\Models\CrmTag;
use App\Models\User;
use App\Support\AuditLog;
use App\Support\PhoneFormatter;
use Livewire\Component;

class ContactShow extends Component
{
    public CrmContact $contact;

    public string $activityType = 'nota';
    public string $activityDescription = '';
    public string $activityDueDate = '';

    public bool $showDealModal = false;
    public string $dealTitle = '';
    public string $dealValue = '';

    public string $newTagName = '';

    public function mount(CrmContact $contact)
    {
        abort_if(auth()->user()->isVendedor() && $contact->assigned_to !== auth()->id(), 403);

        $this->contact = $contact;

        AuditLog::record('view_contact', "visualizou o contato {$contact->name}");
    }

    public function updateField($field, $value)
    {
        abort_if(auth()->user()->isVendedor() && $this->contact->assigned_to !== auth()->id(), 403);

        if (!in_array($field, ['name', 'email', 'phone', 'status', 'assigned_to'], true)) {
            return;
        }

        if ($field === 'phone') {
            $value = PhoneFormatter::toE164($value ?: null);
        }

        $this->contact->update([$field => $value ?: null]);
        $this->contact->refresh();
    }

    public function toggleArchive()
    {
        $willArchive = !$this->contact->archived_at;

        $this->contact->update([
            'archived_at' => $willArchive ? now() : null,
        ]);
        $this->contact->refresh();

        AuditLog::record(
            $willArchive ? 'archive_contact' : 'unarchive_contact',
            ($willArchive ? 'arquivou' : 'desarquivou') . " o contato {$this->contact->name}"
        );
    }

    public function addTag()
    {
        $name = trim($this->newTagName);
        if ($name === '') return;

        $tag = CrmTag::firstOrCreate(['name' => $name]);
        $this->contact->tags()->syncWithoutDetaching([$tag->id]);
        $this->newTagName = '';
        $this->contact->refresh();
    }

    public function removeTag($tagId)
    {
        $this->contact->tags()->detach($tagId);
        $this->contact->refresh();
    }

    public function addActivity()
    {
        $data = $this->validate([
            'activityType'        => 'required|in:nota,ligacao,email,whatsapp,reuniao,tarefa',
            'activityDescription'  => 'required|string|max:2000',
            'activityDueDate'      => 'nullable|date',
        ]);

        CrmActivity::create([
            'crm_contact_id' => $this->contact->id,
            'user_id'        => auth()->id(),
            'type'           => $data['activityType'],
            'description'    => $data['activityDescription'],
            'due_date'       => $data['activityDueDate'] ?: null,
        ]);

        $this->reset(['activityDescription', 'activityDueDate']);
        $this->activityType = 'nota';
        $this->contact->refresh();
    }

    public function completeActivity($activityId)
    {
        $activity = CrmActivity::where('crm_contact_id', $this->contact->id)->findOrFail($activityId);
        $activity->update(['completed_at' => now()]);
        $this->contact->refresh();
    }

    public function createDeal()
    {
        $data = $this->validate([
            'dealTitle' => 'required|string|max:255',
            'dealValue' => 'nullable|numeric|min:0',
        ]);

        CrmDeal::create([
            'crm_contact_id'   => $this->contact->id,
            'assigned_to'      => $this->contact->assigned_to,
            'title'            => $data['dealTitle'],
            'value'            => $data['dealValue'] ?: 0,
            'stage'            => 'novo_lead',
            'stage_changed_at' => now(),
        ]);

        $this->reset(['dealTitle', 'dealValue']);
        $this->showDealModal = false;
        $this->contact->refresh();
    }

    public function render()
    {
        $this->contact->load(['deals', 'activities.author', 'assignee', 'user', 'tags']);

        $whatsappNumber = $this->contact->phone ? ltrim($this->contact->phone, '+') : null;

        $messageTemplates = CrmMessageTemplate::orderBy('name')->get()->map(function ($template) use ($whatsappNumber) {
            $rendered = $template->render($this->contact);

            return (object) [
                'id'          => $template->id,
                'name'        => $template->name,
                'rendered'    => $rendered,
                'whatsappUrl' => $whatsappNumber ? 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($rendered) : null,
            ];
        });

        return view('livewire.admin.crm.contact-show', [
            'staff'            => User::whereIn('role', ['admin', 'vendedor'])->orderBy('name')->get(),
            'messageTemplates' => $messageTemplates,
        ])->extends('layouts.admin', ['title' => $this->contact->name . ' | CRM']);
    }
}
