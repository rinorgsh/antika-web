<?php

namespace App\Mail;

use App\Models\CallbackRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Alerte à l'équipe : un visiteur demande à être rappelé. */
class CallbackRequestMail extends Mailable
{
    public function __construct(public CallbackRequest $callback)
    {
        $this->locale('fr');
    }

    public function envelope(): Envelope
    {
        $c = $this->callback;

        return new Envelope(
            subject: "À rappeler : {$c->name} — {$c->phone}".($c->guest_count ? ", {$c->guest_count} pers." : ''),
            replyTo: $c->email ? [$c->email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.callback-admin', with: [
            'adminUrl' => url('/admin/callbacks'),
        ]);
    }
}
