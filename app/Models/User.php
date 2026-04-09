<?php

namespace App\Models;

use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }


    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isOrganizer(): bool  { return $this->role === 'organizer'; }
    public function isCoach(): bool      { return $this->role === 'coach'; }
    public function isPlayer(): bool     { return $this->role === 'player'; }
    public function isFan(): bool        { return $this->role === 'fan'; }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }


    public function tournaments()
    {
        return $this->hasMany(Tournament::class, 'organizer_id');
    }

    public function coachedTeam()
    {
        return $this->hasOne(Team::class, 'coach_id');
    }

    public function playerProfile()
    {
        return $this->hasOne(PlayerProfile::class);
    }

    public function organizerProfile()
    {
        return $this->hasOne(OrganizerProfile::class);
    }

    public function coachProfile()
    {
        return $this->hasOne(CoachProfile::class);
    }
}