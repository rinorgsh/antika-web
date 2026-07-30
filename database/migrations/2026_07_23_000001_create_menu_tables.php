<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modèle de données de la carte Antika (idempotent : create seulement si absent,
 * pour survivre à un déploiement/migrate précédemment interrompu sur MySQL).
 *
 *   - food     -> food-data.js      (nourriture)
 *   - desserts -> desserts-data.js  (desserts & digestifs)
 *   - drinks   -> boisson-data.js   (boissons)
 *
 * Les champs traduits (NL/FR/EN/AL) sont stockés en JSON : {"nl":"…","fr":"…"}.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_categories')) {
            Schema::create('menu_categories', function (Blueprint $table) {
                $table->id();
                $table->string('surface')->index();      // food | desserts | drinks
                $table->string('slug');                   // entrees, apero, hotdrinks…
                $table->unsignedInteger('position')->default(0);
                $table->boolean('has_note')->default(false);
                $table->json('title');                    // {nl,fr,en,al}
                $table->json('subtitle')->nullable();     // libellé "eyebrow"
                $table->json('note')->nullable();         // note de section (grillades)
                $table->timestamps();
                $table->unique(['surface', 'slug']);
            });
        }

        if (! Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('menu_categories')->cascadeOnDelete();
                $table->string('slug');                   // clé item (k)
                $table->unsignedInteger('position')->default(0);

                $table->boolean('is_subheader')->default(false); // ligne sous-titre (boissons {sub})

                $table->string('price')->nullable();      // "12,50" (null si variantes)
                $table->boolean('per_person')->default(false);   // pp -> "/ pers."
                $table->boolean('is_zero')->default(false);      // mocktail 0%
                $table->boolean('has_description')->default(false); // afficher le descriptif (boissons)

                $table->json('tags')->nullable();         // ["min2","sundayonly"]
                $table->json('variants')->nullable();     // [["glass","5,50"], …]

                $table->string('photo')->nullable();      // fichier photo
                $table->string('logo')->nullable();       // logo de marque
                $table->string('link')->nullable();       // lien externe (sweyn.beer…)

                $table->string('default_name')->nullable(); // n:"…" (identique toutes langues)
                $table->json('name')->nullable();         // nom traduit {nl,fr,en,al}
                $table->json('description')->nullable();  // descriptif traduit

                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_chef')) {
            Schema::create('menu_chef', function (Blueprint $table) {
                $table->id();
                $table->string('image')->nullable();
                $table->string('price')->nullable();
                $table->json('eyebrow')->nullable();
                $table->json('title')->nullable();
                $table->json('description')->nullable();
                $table->json('plabel')->nullable();
                $table->json('preps')->nullable();        // {nl:[[titre,desc]…], fr:[…]}
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_settings')) {
            Schema::create('menu_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->json('value')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('menu_chef');
        Schema::dropIfExists('menu_settings');
    }
};
