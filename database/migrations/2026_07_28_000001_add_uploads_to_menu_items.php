<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Colonnes d'upload : quand le responsable téléverse une photo/un logo,
 * le fichier est stocké sur le disque Laravel (chemin ici). À la publication,
 * il est poussé dans le repo GitHub puis la référence (photo/logo) est mise à jour.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('photo_upload')->nullable()->after('photo');
            $table->string('logo_upload')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['photo_upload', 'logo_upload']);
        });
    }
};
