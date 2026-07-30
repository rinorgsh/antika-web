<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Interrupteur Actif / Inactif : masquer un plat (ou la suggestion du chef)
 * de la carte QR sans le supprimer. Par défaut tout est actif.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_items', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('position');
            }
        });

        Schema::table('menu_chef', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_chef', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', fn (Blueprint $t) => $t->dropColumn('is_active'));
        Schema::table('menu_chef', fn (Blueprint $t) => $t->dropColumn('is_active'));
    }
};
