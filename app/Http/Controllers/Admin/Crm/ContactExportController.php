<?php
namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use App\Support\AuditLog;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactExportController extends Controller
{
    public function export(): StreamedResponse
    {
        $filename = 'contatos-crm-' . now()->format('Y-m-d') . '.csv';

        AuditLog::record('export_contacts', 'exportou a lista de contatos (CSV)');

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nome', 'E-mail', 'Telefone', 'Origem', 'Status', 'Responsável', 'Arquivado', 'Criado em']);

            CrmContact::with('assignee')->chunk(200, function ($contacts) use ($handle) {
                foreach ($contacts as $contact) {
                    fputcsv($handle, [
                        $contact->id,
                        $contact->name,
                        $contact->email,
                        $contact->phone,
                        $contact->source_label,
                        $contact->status_label,
                        $contact->assignee?->name,
                        $contact->archived_at ? 'Sim' : 'Não',
                        $contact->created_at->format('d/m/Y H:i'),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
