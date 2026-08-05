<?php
namespace App\Http\Controllers;

use App\Mail\CrmAbandonedLeadsMail;
use App\Mail\CrmOverdueFollowupsMail;
use App\Models\CrmActivity;
use App\Models\CrmDeal;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Crm\EmailSyncService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Process\Process;

class CronController extends Controller
{
    private array $limits = [
        'cartao' => 30,        // minutos
        'pix'    => 40,        // minutos
        'boleto' => 3 * 1440,  // 3 dias em minutos
    ];

    public function verificarPagamentos(Request $request)
    {
        if ($request->get('token') !== config('app.cron_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stripe   = new StripeService();
        $updated  = 0;
        $expired  = 0;

        Order::where('payment_status', 'pending')
            ->whereNotNull('stripe_id')
            ->orderBy('id')
            ->chunk(50, function ($orders) use ($stripe, &$updated, &$expired) {
                foreach ($orders as $order) {
                    $limitMinutes = $this->limits[$order->payment_method] ?? null;

                    // Cancelar pedidos que passaram do prazo
                    if ($limitMinutes && $order->created_at->addMinutes($limitMinutes)->isPast()) {
                        $order->update([
                            'payment_status' => 'failed',
                            'status'         => 'cancelled',
                        ]);
                        $order->markCrmDealLost('Pagamento expirado');
                        $expired++;
                        continue;
                    }

                    // Consultar Stripe para os que ainda estão no prazo
                    $intent = $stripe->retrievePaymentIntent($order->stripe_id);
                    if (!$intent) continue;

                    $newStatus = match($intent->status) {
                        'succeeded'                            => 'paid',
                        'canceled', 'requires_payment_method' => 'failed',
                        default                                => null,
                    };

                    if ($newStatus) {
                        $order->update(['payment_status' => $newStatus]);
                        if ($newStatus === 'paid') {
                            $order->decrementStock();
                            $order->markCrmDealWon();
                        } elseif ($newStatus === 'failed') {
                            $order->markCrmDealLost('Pagamento falhou');
                        }
                        $updated++;
                    }
                }
            });

        return response()->json([
            'ok'      => true,
            'updated' => $updated,
            'expired' => $expired,
            'at'      => now()->toDateTimeString(),
        ]);
    }

    public function verificarFollowups(Request $request)
    {
        if ($request->get('token') !== config('app.cron_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $emailHour = (int) \App\Models\SiteSetting::get('crm_email_hour', '8');
        $lastRun   = \App\Models\SiteSetting::get('crm_followups_last_run', '');

        if (now()->hour < $emailHour || $lastRun === now()->toDateString()) {
            return response()->json(['ok' => true, 'skipped' => true, 'at' => now()->toDateTimeString()]);
        }

        \App\Models\SiteSetting::set('crm_followups_last_run', now()->toDateString());

        $overdue = CrmActivity::with('contact')
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->get();

        $emailsSent = 0;

        $byAssignee = $overdue
            ->filter(fn ($activity) => $activity->contact && $activity->contact->assigned_to)
            ->groupBy(fn ($activity) => $activity->contact->assigned_to);

        foreach ($byAssignee as $userId => $activities) {
            $user = User::find($userId);
            if (!$user) continue;

            Mail::to($user->email)->send(new CrmOverdueFollowupsMail($user, $activities));
            $emailsSent++;
        }

        if ($overdue->isNotEmpty()) {
            User::where('role', 'admin')->each(function ($admin) use ($overdue, &$emailsSent) {
                Mail::to($admin->email)->send(new CrmOverdueFollowupsMail($admin, $overdue));
                $emailsSent++;
            });
        }

        return response()->json([
            'ok'          => true,
            'overdue'     => $overdue->count(),
            'emails_sent' => $emailsSent,
            'at'          => now()->toDateTimeString(),
        ]);
    }

    public function verificarEstagiosParados(Request $request)
    {
        if ($request->get('token') !== config('app.cron_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $taskDescription = 'Enviar mensagem de acompanhamento';
        $created = 0;

        $stalled = CrmDeal::where('stage', 'proposta')
            ->where('stage_changed_at', '<', now()->subDays(3))
            ->get();

        foreach ($stalled as $deal) {
            $alreadyOpen = CrmActivity::where('crm_deal_id', $deal->id)
                ->where('description', $taskDescription)
                ->whereNull('completed_at')
                ->exists();

            if ($alreadyOpen) continue;

            CrmActivity::create([
                'crm_contact_id' => $deal->crm_contact_id,
                'crm_deal_id'    => $deal->id,
                'type'           => 'tarefa',
                'description'    => $taskDescription,
                'due_date'       => now(),
            ]);

            $created++;
        }

        $abandonedEmailSent = $this->notifyAbandonedLeads();

        return response()->json([
            'ok'                   => true,
            'stalled'              => $stalled->count(),
            'created'              => $created,
            'abandoned_email_sent' => $abandonedEmailSent,
            'at'                   => now()->toDateTimeString(),
        ]);
    }

    protected function notifyAbandonedLeads(): bool
    {
        $emailHour = (int) SiteSetting::get('crm_email_hour', '8');
        $lastRun   = SiteSetting::get('crm_abandoned_leads_last_run', '');

        if (now()->hour < $emailHour || $lastRun === now()->toDateString()) {
            return false;
        }

        $abandoned = CrmDeal::with('contact')
            ->where('stage', 'novo_lead')
            ->where('created_at', '<', now()->subDay())
            ->get();

        if ($abandoned->isEmpty()) {
            return false;
        }

        SiteSetting::set('crm_abandoned_leads_last_run', now()->toDateString());

        User::where('role', 'admin')->each(function ($admin) use ($abandoned) {
            Mail::to($admin->email)->send(new CrmAbandonedLeadsMail($admin, $abandoned));
        });

        return true;
    }

    public function sincronizarEmails(Request $request, EmailSyncService $emailSync)
    {
        if ($request->get('token') !== config('app.cron_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $saved = $emailSync->syncAll();

        return response()->json([
            'ok'    => true,
            'saved' => $saved,
            'at'    => now()->toDateTimeString(),
        ]);
    }

    public function backupBanco(Request $request)
    {
        if ($request->get('token') !== config('app.cron_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $file = $dir . '/backup-' . now()->format('Y-m-d_His') . '.sql';

        $process = new Process([
            config('app.mysqldump_path'),
            '--host=' . config('database.connections.mysql.host'),
            '--port=' . config('database.connections.mysql.port'),
            '--user=' . config('database.connections.mysql.username'),
            '--password=' . config('database.connections.mysql.password'),
            '--single-transaction',
            '--result-file=' . $file,
            config('database.connections.mysql.database'),
        ]);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful() || !File::exists($file) || File::size($file) === 0) {
            File::delete($file);
            return response()->json(['ok' => false, 'error' => $process->getErrorOutput()], 500);
        }

        // Mantém só os últimos 14 dias de backup local
        $pruned = 0;
        foreach (File::files($dir) as $existing) {
            $path = $existing->getPathname();
            if (now()->diffInDays(\Illuminate\Support\Carbon::createFromTimestamp(File::lastModified($path))) > 14) {
                File::delete($path);
                $pruned++;
            }
        }

        return response()->json([
            'ok'      => true,
            'file'    => basename($file),
            'size_kb' => round(File::size($file) / 1024, 1),
            'pruned'  => $pruned,
            'at'      => now()->toDateTimeString(),
        ]);
    }
}
