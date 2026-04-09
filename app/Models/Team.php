<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'sport_id',
        'coach_id',
        'logo_url',
        'founded_at',
    ];

    protected function casts(): array
    {
        return [
            'founded_at' => 'date',
        ];
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function playerProfiles()
    {
        return $this->hasMany(PlayerProfile::class);
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'registrations')
                    ->withPivot('registration_date', 'status', 'notes')
                    ->withTimestamps();
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function matchTeams()
    {
        return $this->hasMany(MatchTeam::class);
    }
}