<?php
namespace App\Services\Crm;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailSyncService
{
    public function __construct(private ChannelMessageService $messages) {}

    public function syncAll(): int
    {
        $total = 0;

        foreach (User::whereNotNull('imap_host')->get() as $vendedor) {
            $total += $this->syncMailbox($vendedor);
        }

        return $total;
    }

    public function syncMailbox(User $vendedor): int
    {
        $mailbox = $this->buildMailboxString($vendedor);
        $conn = @imap_open($mailbox, $vendedor->imap_username, $vendedor->imap_password);

        if (!$conn) {
            Log::warning("EmailSync: falha ao conectar na caixa do vendedor {$vendedor->id}: " . imap_last_error());
            return 0;
        }

        $since = now()->subDays(2)->format('d-M-Y');
        $ids = imap_search($conn, "SINCE \"{$since}\"", SE_UID) ?: [];
        $saved = 0;

        foreach ($ids as $uid) {
            if ($this->processMessage($conn, $uid, $vendedor)) {
                $saved++;
            }
        }

        imap_close($conn);

        return $saved;
    }

    protected function buildMailboxString(User $vendedor): string
    {
        $encryption = match($vendedor->imap_encryption) {
            'ssl' => '/imap/ssl',
            'tls' => '/imap/tls',
            default => '/imap/novalidate-cert',
        };

        return "{{$vendedor->imap_host}:{$vendedor->imap_port}{$encryption}}" . ($vendedor->imap_folder ?: 'INBOX');
    }

    protected function processMessage($conn, int $uid, User $vendedor): bool
    {
        $header = imap_fetchheader($conn, $uid, FT_UID);
        $info = imap_rfc822_parse_headers($header);

        if (!$info) {
            return false;
        }

        $messageId = trim($info->message_id ?? '', '<> ');
        if ($messageId === '') {
            return false;
        }

        $fromEmail = strtolower(($info->from[0]->mailbox ?? '') . '@' . ($info->from[0]->host ?? ''));
        $fromName  = $info->from[0]->personal ?? null;
        $toEmail   = strtolower(($info->to[0]->mailbox ?? '') . '@' . ($info->to[0]->host ?? ''));

        $isFromVendedor = $fromEmail === strtolower($vendedor->email) || $fromEmail === strtolower((string) $vendedor->imap_username);
        $direction = $isFromVendedor ? 'enviada' : 'recebida';
        $counterpartEmail = $isFromVendedor ? $toEmail : $fromEmail;
        $counterpartName  = $isFromVendedor ? null : $fromName;

        if (!$counterpartEmail || !str_contains($counterpartEmail, '@')) {
            return false;
        }

        $contact = $this->messages->resolveContactByEmail($counterpartEmail, $counterpartName);
        $body = $this->fetchTextBody($conn, $uid);
        $occurredAt = isset($info->date) ? \Illuminate\Support\Carbon::parse($info->date) : now();

        $message = $this->messages->log($contact, [
            'channel'             => 'email',
            'direction'           => $direction,
            'subject'             => imap_utf8($info->subject ?? ''),
            'content'             => $body,
            'occurred_at'         => $occurredAt,
            'external_message_id' => $messageId,
            'user_id'             => $vendedor->id,
        ]);

        return $message->wasRecentlyCreated;
    }

    protected function fetchTextBody($conn, int $uid): string
    {
        $structure = imap_fetchstructure($conn, $uid, FT_UID);

        if (!empty($structure->parts)) {
            foreach ($structure->parts as $index => $part) {
                if ($part->subtype === 'PLAIN') {
                    return imap_fetchbody($conn, $uid, (string) ($index + 1), FT_UID | FT_PEEK);
                }
            }
            return imap_fetchbody($conn, $uid, '1', FT_UID | FT_PEEK);
        }

        return imap_body($conn, $uid, FT_UID | FT_PEEK);
    }
}
