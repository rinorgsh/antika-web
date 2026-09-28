<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * La description FR du domaine complet citait encore l'ancien nom
 * « Antika Baba » (plateforme baba-event). Relançable sans effet.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('venues')->where('description', 'like', '%Antika Baba%')->get() as $venue) {
            $description = json_decode($venue->description, true);
            if (! is_array($description)) {
                continue;
            }
            $description = array_map(fn ($text) => is_string($text) ? str_replace('Antika Baba', 'Antika', $text) : $text, $description);
            DB::table('venues')->where('id', $venue->id)->update(['description' => json_encode($description, JSON_UNESCAPED_UNICODE)]);
        }
    }

    public function down(): void
    {
        // Données : rien à annuler.
    }
};
