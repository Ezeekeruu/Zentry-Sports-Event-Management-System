<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZentryMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'match_date',
        'match_time',
        'venue',
        'status',
        'round_name',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'date',
        ];
    }

    public function scopeScheduled($query) { return $query->where('status', 'scheduled'); }
    public function scopeLive($query)      { return $query->where('status', 'live'); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function matchTeams()
    {
        return $this->hasMany(MatchTeam::class, 'match_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'match_teams', 'match_id', 'team_id')
                    ->withPivot('points_scored', 'rank_position', 'seed_number')
                    ->withTimestamps();
    }
}