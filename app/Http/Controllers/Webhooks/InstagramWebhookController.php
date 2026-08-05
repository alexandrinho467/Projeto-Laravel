<?php
namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Crm\ChannelMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InstagramWebhookController extends Controller
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
            foreach ($entry['messaging'] ?? [] as $event) {
                $this->processEvent($event);
            }
        }

        return response()->json(['ok' => true]);
    }

    protected function verify(Request $request)
    {
        $mode      = $request->get('hub_mode');
        $token     = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.instagram.verify_token')) {
            return response($challenge, 200);
        }

        return response()->json(['error' => 'Invalid verify token'], 403);
    }

    protected function hasValidSignature(Request $request): bool
    {
        $secret = config('services.instagram.app_secret');
        if (!$secret) {
            return false;
        }

        $signature = $request->header('X-Hub-Signature-256', '');
        $expected  = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    protected function processEvent(array $event): void
    {
        $igsid = $event['sender']['id'] ?? null;
        $text  = $event['message']['text'] ?? null;

        // Eventos sem mensagem de texto (delivery/read receipts, echoes de mídia) são ignorados por ora.
        if (!$igsid || $text === null) {
            return;
        }

        $contact = $this->messages->resolveContactByInstagram($igsid);

        $this->messages->log($contact, [
            'channel'             => 'instagram',
            'direction'           => 'recebida',
            'content'             => $text,
            'occurred_at'         => isset($event['timestamp']) ? Carbon::createFromTimestampMs((int) $event['timestamp']) : now(),
            'external_message_id' => $event['message']['mid'] ?? null,
            'raw_payload'         => $event,
        ]);
    }
}
