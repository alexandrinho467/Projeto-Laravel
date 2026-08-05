<?php
namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Crm\ChannelMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WhatsAppWebhookController extends Controller
{
    public function __construct(private ChannelMessageService $messages) {}

    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->verify($request);
        }

        if (!$this->hasValidSignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $this->processChange($change['value'] ?? []);
            }
        }

        return response()->json(['ok' => true]);
    }

    protected function verify(Request $request)
    {
        // PHP normaliza pontos para underscores em nomes de query params (hub.mode -> hub_mode).
        $mode      = $request->get('hub_mode');
        $token     = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200);
        }

        return response()->json(['error' => 'Invalid verify token'], 403);
    }

    protected function hasValidSignature(Request $request): bool
    {
        $secret = config('services.whatsapp.app_secret');
        if (!$secret) {
            return false;
        }

        $signature = $request->header('X-Hub-Signature-256', '');
        $expected  = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    protected function processChange(array $value): void
    {
        $profiles = collect($value['contacts'] ?? [])->keyBy('wa_id');

        foreach ($value['messages'] ?? [] as $msg) {
            $waId = $msg['from'] ?? null;
            if (!$waId) {
                continue;
            }

            $profileName = $profiles->get($waId)['profile']['name'] ?? null;
            $contact = $this->messages->resolveContactByWhatsapp($waId, $profileName);

            $this->messages->log($contact, [
                'channel'             => 'whatsapp',
                'direction'           => 'recebida',
                'content'             => $msg['text']['body'] ?? ('[' . ($msg['type'] ?? 'mídia') . ']'),
                'occurred_at'         => isset($msg['timestamp']) ? Carbon::createFromTimestamp((int) $msg['timestamp']) : now(),
                'external_message_id' => $msg['id'] ?? null,
                'raw_payload'         => $msg,
            ]);
        }
    }
}
