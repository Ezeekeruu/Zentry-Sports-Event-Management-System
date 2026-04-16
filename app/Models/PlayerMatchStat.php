<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerMatchStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_profile_id',
        'match_team_id',
        'points',
    ];

    public function playerProfile()
    {
        return $this->belongsTo(PlayerProfile::class);
    }

    public function matchTeam()
    {
        return $this->belongsTo(MatchTeam::class);
    }
}
