<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmAuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogViewer extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.crm.audit-log-viewer', [
            'logs' => CrmAuditLog::with('user')->latest('created_at')->paginate(30),
        ])->extends('layouts.admin', ['title' => 'Auditoria | CRM']);
    }
}
