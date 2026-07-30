<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Colonnes d'upload photo/logo (idempotent : n'ajoute que si absent).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_items', 'photo_upload')) {
                $table->string('photo_upload')->nullable()->after('photo');
            }
            if (! Schema::hasColumn('menu_items', 'logo_upload')) {
                $table->string('logo_upload')->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['photo_upload', 'logo_upload']);
        });
    }
};
