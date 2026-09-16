<?php

namespace App\Services;

use App\Models\EventSetting;
use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * PDF d'un devis.
 *  - showPrices = false : récapitulatif envoyé au client (sans prix, dans sa langue) ;
 *  - showPrices = true  : devis chiffré pour l'équipe (admin).
 */
class QuotePdf
{
    public static function make(Quote $quote, bool $showPrices, ?string $locale = null): \Barryvdh\DomPDF\PDF
    {
        $quote->loadMissing(Quote::FULL);

        return Pdf::loadView('pdf.quote', [
            'quote' => $quote,
            'showPrices' => $showPrices,
            'l' => $locale ?? ($showPrices ? 'fr' : $quote->locale),
            'company' => [
                'name' => EventSetting::get('company_name'),
                'address' => EventSetting::get('address'),
                'phone' => EventSetting::get('phone'),
                'email' => EventSetting::get('email'),
            ],
            'logo' => is_file(public_path('images/logo.png')) ? public_path('images/logo.png') : null,
        ]);
    }
}
