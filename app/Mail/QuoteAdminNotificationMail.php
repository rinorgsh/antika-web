<?php

namespace App\Mail;

use App\Models\Quote;
use App\Services\QuotePdf;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Alerte envoyée à l'équipe à chaque nouvelle demande (en français, avec prix). */
class QuoteAdminNotificationMail extends Mailable
{
    public function __construct(public Quote $quote)
    {
        $this->locale('fr');
    }

    public function envelope(): Envelope
    {
        $c = $this->quote->customer;

        return new Envelope(
            subject: "Nouvelle demande {$this->quote->quote_number} — {$c->full_name}, {$this->quote->guestCount()} pers. le {$this->quote->event_date?->format('d/m/Y')}",
            replyTo: [$c->email],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.quote-admin', with: [
            'adminUrl' => url('/admin/quotes/'.$this->quote->id),
        ]);
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => QuotePdf::make($this->quote, showPrices: true)->output(),
                "devis-{$this->quote->quote_number}.pdf",
            )->withMime('application/pdf'),
        ];
    }
}
