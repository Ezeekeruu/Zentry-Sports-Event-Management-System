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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_name');
            $table->foreignId('organizer_id')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->foreignId('sport_id')
                  ->constrained('sports')
                  ->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])
                  ->default('upcoming');
            $table->unsignedSmallInteger('max_teams')->default(16);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
