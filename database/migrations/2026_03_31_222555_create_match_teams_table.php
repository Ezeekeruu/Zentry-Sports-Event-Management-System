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
        Schema::create('match_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')
                  ->constrained('matches')
                  ->cascadeOnDelete();
            $table->foreignId('team_id')
                  ->constrained('teams')
                  ->restrictOnDelete();
            $table->unsignedInteger('points_scored')->nullable();
            $table->unsignedTinyInteger('rank_position')->nullable();
            $table->unsignedTinyInteger('seed_number')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_teams');
    }
};
