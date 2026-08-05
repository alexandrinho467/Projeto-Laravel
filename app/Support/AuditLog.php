<?php
namespace App\Support;

use App\Models\CrmAuditLog;

class AuditLog
{
    public static function record(string $action, string $description): void
    {
        try {
            CrmAuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'description' => $description,
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Falha ao gravar log de auditoria: ' . $e->getMessage());
        }
    }
}
