<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Simulateur de devis « location de salle / événements », repris de l'ancienne
 * application baba-event (baba-event.on-forge.com).
 *
 * Différences avec l'original :
 *  - les textes visibles par le client sont traduits : colonnes JSON {nl, fr, en} ;
 *  - les tables de menu sont préfixées event_ : menu_categories et menu_items
 *    existent déjà pour la carte du restaurant ;
 *  - statuts et types en chaînes (plus d'enum MySQL, qui refusait déjà des
 *    valeurs utilisées par l'ancien back-office) ;
 *  - rôle explicite des catégories de boissons (apéritif, bulles) au lieu
 *    d'une détection par mots-clés dans le nom, qui cassait à la traduction ;
 *  - langue du client et provenance publicitaire (gclid, utm) sur le devis.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->json('short_description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('capacity_seated')->nullable();
            $table->integer('capacity_standing')->nullable();
            $table->boolean('has_parking')->default(true);
            $table->boolean('has_vestiaire')->default(false);
            $table->boolean('has_private_toilets')->default(true);
            $table->boolean('has_kitchen')->default(false);
            $table->integer('surface_area')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('venue_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_menu_formulas', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->json('short_description')->nullable();
            $table->decimal('price_per_person', 8, 2)->default(0);
            $table->string('type', 20)->default('seated');   // seated | buffet
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_menu_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_formula_id')->constrained('event_menu_formulas')->cascadeOnDelete();
            $table->json('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('event_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_category_id')->constrained('event_menu_categories')->cascadeOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->decimal('supplement_price', 8, 2)->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('drink_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->string('role', 20)->nullable();   // null (boissons) | aperitif | bubbles
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('drink_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drink_category_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price_per_unit', 8, 2)->nullable();
            $table->decimal('price_all_in', 8, 2)->nullable();
            $table->string('unit_type', 20)->default('glass');   // glass | bottle | person
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('extra_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('extra_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_category_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('price_type', 20)->default('fixed');   // fixed | per_person | per_unit
            $table->string('exclusive_group')->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_type_id')->nullable()->constrained()->nullOnDelete();
            $table->date('event_date')->nullable();
            $table->string('event_time_slot', 20)->nullable();   // day | evening | full_day
            $table->integer('guest_count_adults');
            $table->integer('guest_count_children')->default(0);
            $table->boolean('child_menu')->default(false);
            $table->text('dietary_requirements')->nullable();
            $table->text('special_requests')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(21);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->decimal('deposit_percentage', 5, 2)->default(30);
            $table->date('valid_until')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('locale', 5)->default('nl');
            $table->json('tracking')->nullable();   // gclid, utm_* : d'où vient la demande
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quote_venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('quote_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_formula_id')->constrained('event_menu_formulas')->cascadeOnDelete();
            $table->integer('guest_count');
            $table->decimal('price_per_person', 8, 2);
            $table->decimal('supplements_total', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('quote_menu_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('event_menu_items')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('quote_drinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drink_option_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->string('pricing_mode', 20);   // per_unit | all_in
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('quote_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('extra_item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->date('event_date')->index();
            $table->string('status', 20)->default('confirmed');
            $table->dateTime('deposit_paid_at')->nullable();
            $table->dateTime('fully_paid_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('event_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'event_settings', 'reservations', 'blocked_dates', 'quote_extras', 'quote_drinks',
            'quote_menu_choices', 'quote_menus', 'quote_venues', 'quotes', 'customers',
            'extra_items', 'extra_categories', 'drink_options', 'drink_categories',
            'event_menu_items', 'event_menu_categories', 'event_menu_formulas',
            'venue_images', 'venues', 'event_types',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
