<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Historique des publications de la carte QR.
 *
 * Avant : « Publier » faisait un commit sur GitHub Pages. Désormais le site
 * sert lui-même la carte (/carte/) : chaque publication fige ici les fichiers
 * de données générés. Les clients voient toujours la dernière ligne, ce qui
 * garde le principe « je modifie, puis je publie ».
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_publications')) {
            return;
        }

        Schema::create('menu_publications', function (Blueprint $table) {
            $table->id();
            $table->longText('files');   // JSON {nom de fichier: contenu}
            $table->string('message')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_publications');
    }
};
