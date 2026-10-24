<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Festival 1912 (temporaire) : demandes de participation sponsors, exposants
 * et donateurs reçues via /sponsors. À supprimer avec la page après le festival.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sponsor_applications')) {
            return;
        }

        Schema::create('sponsor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('participation_type', 30);
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('phone', 50);
            $table->string('email');
            $table->text('activity')->nullable();
            $table->text('promote')->nullable();
            $table->text('sell')->nullable();
            $table->text('message')->nullable();
            $table->string('locale', 5)->default('fr');
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_applications');
    }
};
