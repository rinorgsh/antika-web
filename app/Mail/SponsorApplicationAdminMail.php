<?php

namespace App\Mail;

use App\Models\SponsorApplication;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Alerte à l'équipe : nouvelle demande de participation au Festival 1912. */
class SponsorApplicationAdminMail extends Mailable
{
    public function __construct(public SponsorApplication $application)
    {
        $this->locale('fr');
    }

    public function envelope(): Envelope
    {
        $a = $this->application;
        $type = SponsorApplication::TYPES[$a->participation_type] ?? $a->participation_type;

        return new Envelope(
            subject: "Festival 1912 — {$type} : ".($a->organization ?: $a->name),
            replyTo: [$a->email],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.sponsor-admin', with: [
            'adminUrl' => url("/admin/festival-1912/{$this->application->id}"),
        ]);
    }
}
