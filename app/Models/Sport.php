<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_name',
        'min_teams_per_match',
        'max_teams_per_match',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)   { return $query->where('is_active', true); }
    public function scopeArchived($query) { return $query->where('is_active', false); }

    public function teams()       { return $this->hasMany(Team::class); }
    public function tournaments() { return $this->hasMany(Tournament::class); }
}