<?php

namespace App\Mail;

use App\Models\EventSetting;
use App\Models\SponsorApplication;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Accusé de réception envoyé au candidat, dans la langue choisie sur la page. */
class SponsorApplicationCustomerMail extends Mailable
{
    public function __construct(public SponsorApplication $application)
    {
        $this->locale($application->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trans('sponsors.email.subject', [], $this->application->locale),
            replyTo: [EventSetting::get('email')],
        );
    }

    public function content(): Content
    {
        $l = $this->application->locale;

        return new Content(view: 'emails.sponsor-customer', with: [
            's' => trans('sponsors', [], $l),
            'l' => $l,
        ]);
    }
}
