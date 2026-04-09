<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                  ->constrained('teams')
                  ->cascadeOnDelete();
            $table->foreignId('tournament_id')
                  ->constrained('tournaments')
                  ->cascadeOnDelete();
            $table->date('registration_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'tournament_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
