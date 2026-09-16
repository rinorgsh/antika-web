<?php

namespace App\Mail;

use App\Models\EventSetting;
use App\Models\Quote;
use App\Services\QuotePdf;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Accusé de réception envoyé au client, dans sa langue, avec le récapitulatif (sans prix). */
class QuoteCustomerMail extends Mailable
{
    public function __construct(public Quote $quote)
    {
        $this->locale($quote->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('simulator.email.subject', ['number' => $this->quote->quote_number], $this->quote->locale),
            replyTo: [EventSetting::get('email')],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.quote-customer', with: [
            'l' => $this->quote->locale,
            'phone' => EventSetting::get('phone'),
            'email' => EventSetting::get('email'),
            'whatsapp' => EventSetting::get('whatsapp'),
        ]);
    }

    public function attachments(): array
    {
        $number = $this->quote->quote_number;

        return [
            Attachment::fromData(
                fn () => QuotePdf::make($this->quote, showPrices: false)->output(),
                __('simulator.pdf.filename', ['number' => $number], $this->quote->locale).'.pdf',
            )->withMime('application/pdf'),
        ];
    }
}
