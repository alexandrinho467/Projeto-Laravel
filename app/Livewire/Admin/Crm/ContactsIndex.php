<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmContact;
use App\Models\CrmTag;
use App\Models\User;
use App\Support\AuditLog;
use App\Support\PhoneFormatter;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ContactsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $assignedTo = '';

    #[Url]
    public bool $showArchived = false;

    public bool $showCreateModal = false;
    public bool $showDealsModal = false;
    public ?CrmContact $viewingContact = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $source = 'manual';
    public string $newStatus = 'lead';
    public string $newAssignedTo = '';

    public ?CrmContact $duplicateContact = null;

    public array $selected = [];
    public bool $selectAll = false;
    public string $bulkAssignedTo = '';
    public string $bulkTagName = '';

    public function mount()
    {
        if (auth()->user()->isVendedor()) {
            $this->assignedTo = (string) auth()->id();
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingAssignedTo() { $this->resetPage(); }
    public function updatingShowArchived() { $this->resetPage(); $this->selected = []; }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->visibleContactIds() : [];
    }

    protected function visibleContactIds(): array
    {
        return $this->baseQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function bulkAssign()
    {
        if (empty($this->selected) || $this->bulkAssignedTo === '') return;

        CrmContact::whereIn('id', $this->selected)->update(['assigned_to' => $this->bulkAssignedTo]);
        $this->selected = [];
        $this->selectAll = false;
        $this->bulkAssignedTo = '';
    }

    public function bulkArchive()
    {
        if (empty($this->selected)) return;

        $count = CrmContact::whereIn('id', $this->selected)->update(['archived_at' => now()]);
        AuditLog::record('bulk_archive_contacts', "arquivou {$count} contato(s) em massa");

        $this->selected = [];
        $this->selectAll = false;
    }

    public function bulkAddTag()
    {
        $name = trim($this->bulkTagName);
        if (empty($this->selected) || $name === '') return;

        $tag = CrmTag::firstOrCreate(['name' => $name]);
        $tag->contacts()->syncWithoutDetaching(array_map('intval', $this->selected));

        $this->selected = [];
        $this->selectAll = false;
        $this->bulkTagName = '';
    }

    public function openCreateModal()
    {
        if (auth()->user()->isVendedor()) {
            $this->newAssignedTo = (string) auth()->id();
        }
        $this->duplicateContact = null;
        $this->showCreateModal = true;
    }

    public function verNegociacoes($contactId)
    {
        $this->viewingContact = CrmContact::with(['deals' => fn ($q) => $q->latest()])->find($contactId);
        $this->showDealsModal = true;
    }

    public function createContact()
    {
        $data = $this->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:20',
            'source'        => 'required|in:manual,site,instagram,whatsapp,email,indicacao,outro',
            'newStatus'     => 'required|in:lead,ativo,inativo',
            'newAssignedTo' => 'nullable|exists:users,id',
        ]);

        $this->duplicateContact = null;

        $phone = PhoneFormatter::toE164($data['phone'] ?: null);

        if (!empty($data['email']) || !empty($phone)) {
            $existing = CrmContact::where(function ($q) use ($data, $phone) {
                if (!empty($data['email'])) $q->orWhere('email', $data['email']);
                if (!empty($phone)) $q->orWhere('phone', $phone);
            })->first();

            if ($existing) {
                $this->duplicateContact = $existing;
                return;
            }
        }

        $contact = CrmContact::create([
            'name'        => $data['name'],
            'email'       => $data['email'] ?: null,
            'phone'       => $phone,
            'source'      => $data['source'],
            'status'      => $data['newStatus'],
            'assigned_to' => $data['newAssignedTo'] ?: null,
        ]);

        $this->reset(['name', 'email', 'phone', 'source', 'newStatus', 'newAssignedTo']);
        $this->showCreateModal = false;

        $this->redirect(route('admin.crm.contacts.show', $contact));
    }

    protected function baseQuery()
    {
        $user = auth()->user();

        $query = CrmContact::query();

        if ($this->showArchived) {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        if ($user->isVendedor()) {
            $query->where('assigned_to', $user->id);
        } elseif ($this->assignedTo !== '') {
            $query->where('assigned_to', $this->assignedTo);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function render()
    {
        return view('livewire.admin.crm.contacts-index', [
            'contacts' => $this->baseQuery()->with(['assignee', 'user', 'tags'])->latest()->paginate(20),
            'staff'    => User::whereIn('role', ['admin', 'vendedor'])->orderBy('name')->get(),
        ])->extends('layouts.admin', ['title' => 'Contatos | CRM']);
    }
}
