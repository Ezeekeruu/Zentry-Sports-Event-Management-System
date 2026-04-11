<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_name',
        'organizer_id',
        'sport_id',
        'start_date',
        'end_date',
        'status',
        'max_teams',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_active'  => 'boolean',
        ];
    }

    public function scopeActive($query)    { return $query->where('is_active', true); }
    public function scopeArchived($query)  { return $query->where('is_active', false); }
    public function scopeUpcoming($query)  { return $query->where('status', 'upcoming'); }
    public function scopeOngoing($query)   { return $query->where('status', 'ongoing'); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }

    public function organizer()    { return $this->belongsTo(User::class, 'organizer_id'); }
    public function sport()        { return $this->belongsTo(Sport::class); }
    public function registrations(){ return $this->hasMany(Registration::class); }
    public function matches()      { return $this->hasMany(ZentryMatch::class, 'tournament_id'); }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'registrations')
                    ->withPivot('registration_date', 'status', 'notes')
                    ->withTimestamps();
    }
}