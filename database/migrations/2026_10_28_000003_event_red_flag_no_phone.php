<?php

use App\Models\MenuSetting;
use Illuminate\Database\Migrations\Migration;

/*
 * Soirée « Red Flag » : le client ne veut pas du numéro de réservation en
 * haut de la carte (chanteurs + date suffisent). Champ vide = bloc masqué.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (MenuSetting::get('event.reservation')) {
            MenuSetting::put('event.reservation', '');
        }
    }

    public function down(): void
    {
        // Données de carte : on revient en arrière depuis l'admin.
    }
};
