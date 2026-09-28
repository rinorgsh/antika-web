<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Demandes de rappel : formulaire court (nom, téléphone, date, invités) pour
 * les visiteurs qui ne veulent pas passer par les 6 étapes du simulateur.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('callback_requests')) {
            return;
        }

        Schema::create('callback_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->foreignId('event_type_id')->nullable()->constrained()->nullOnDelete();
            $table->date('event_date')->nullable();
            $table->unsignedInteger('guest_count')->nullable();
            $table->text('message')->nullable();
            $table->string('source_page')->nullable();
            $table->string('locale', 5)->default('nl');
            $table->json('tracking')->nullable();
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('callback_requests');
    }
};
