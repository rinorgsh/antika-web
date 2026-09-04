<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Prépare la surface « event » : une soirée lounge a besoin de deux choses
 * que la carte du restaurant n'a pas.
 *   · menu_items.hint     : la mention en orange à côté du nom (rhum, vodka…)
 *   · menu_categories.banner : le bandeau illustré de la section (event/xxx.jpg)
 * Les deux sont facultatifs et ignorés par les autres surfaces.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('hint')->nullable()->after('default_name');
        });

        Schema::table('menu_categories', function (Blueprint $table) {
            $table->string('banner')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', fn (Blueprint $t) => $t->dropColumn('hint'));
        Schema::table('menu_categories', fn (Blueprint $t) => $t->dropColumn('banner'));
    }
};
