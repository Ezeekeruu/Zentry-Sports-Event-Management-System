<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_profile_id')
                ->constrained('player_profiles')
                ->cascadeOnDelete();
            $table->foreignId('match_team_id')
                ->constrained('match_teams')
                ->cascadeOnDelete();
            $table->unsignedInteger('points')->nullable();
            $table->timestamps();

            $table->unique(['player_profile_id', 'match_team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_match_stats');
    }
};
