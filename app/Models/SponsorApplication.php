<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Festival 1912 : demande de participation (sponsor, exposant, donateur). */
class SponsorApplication extends Model
{
    public const TYPES = [
        'sponsor' => 'Sponsor',
        'exhibitor' => 'Exposant',
        'sponsor_exhibitor' => 'Sponsor & Exposant',
        'donor' => 'Donateur',
    ];

    public const TYPE_COLORS = [
        'sponsor' => 'primary',
        'exhibitor' => 'info',
        'sponsor_exhibitor' => 'success',
        'donor' => 'gray',
    ];

    public const STATUSES = [
        'new' => 'Nouvelle',
        'contacted' => 'Contacté',
        'accepted' => 'Accepté',
        'declined' => 'Refusé',
    ];

    public const STATUS_COLORS = [
        'new' => 'warning',
        'contacted' => 'info',
        'accepted' => 'success',
        'declined' => 'gray',
    ];

    /** Langues de la page /sponsors (indépendantes de celles du site Antika). */
    public const LOCALES = ['fr', 'nl', 'sq'];

    public const LOCALE_LABELS = ['fr' => 'Français', 'nl' => 'Néerlandais', 'sq' => 'Albanais'];

    protected $guarded = [];
}
