<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CrmAbandonedLeadsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $recipient, public Collection $deals) {}

    public function envelope(): Envelope
    {
        $count = $this->deals->count();

        return new Envelope(
            subject: $count === 1
                ? '1 lead abandonado há 24h+ no CRM — Dias Sneakers'
                : "{$count} leads abandonados há 24h+ no CRM — Dias Sneakers"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.crm-abandoned-leads');
    }
}
