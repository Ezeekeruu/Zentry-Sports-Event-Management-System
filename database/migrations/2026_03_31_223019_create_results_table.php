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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_team_id')
                  ->unique()
                  ->constrained('match_teams')
                  ->cascadeOnDelete();
            $table->text('summary')->nullable();
            $table->unsignedTinyInteger('total_teams')->default(2);
            $table->unsignedInteger('highest_score')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
