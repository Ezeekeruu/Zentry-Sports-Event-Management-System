<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'team_id',
        'points_scored',
        'rank_position',
        'seed_number',
    ];

    public function match()
    {
        return $this->belongsTo(ZentryMatch::class, 'match_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }

    public function playerStats()
    {
        return $this->hasMany(PlayerMatchStat::class);
    }
}