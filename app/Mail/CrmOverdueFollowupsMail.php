<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CrmOverdueFollowupsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $recipient, public Collection $activities) {}

    public function envelope(): Envelope
    {
        $count = $this->activities->count();

        return new Envelope(
            subject: $count === 1
                ? '1 tarefa atrasada no CRM — Dias Sneakers'
                : "{$count} tarefas atrasadas no CRM — Dias Sneakers"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.crm-overdue-followups');
    }
}
