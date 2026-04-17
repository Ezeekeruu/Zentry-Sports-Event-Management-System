<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_match_stats', function (Blueprint $table) {
            $table->json('stat_line')->nullable()->after('points');
        });
    }

    public function down(): void
    {
        Schema::table('player_match_stats', function (Blueprint $table) {
            $table->dropColumn('stat_line');
        });
    }
};
