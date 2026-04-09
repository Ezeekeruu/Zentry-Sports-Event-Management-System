<?php

namespace App\Observers;

use App\Models\CoachProfile;
use App\Models\OrganizerProfile;
use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    
     //Auto-creates the matching specialization profile row
    public function created(User $user): void
    {
        match ($user->role) {

            'organizer' => OrganizerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_name' => null,
                    'contact_number'    => null,
                ]
            ),

            'coach' => CoachProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization'   => null,
                    'years_experience' => 0,
                ]
            ),

            'player' => PlayerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'team_id'       => null,
                    'position'      => null,
                    'jersey_number' => null,
                ]
            ),

            default => null,
        };
    }

    // Fires when a user's role is changed and Removes old profile and creates new one
    
    public function updated(User $user): void
    {
        if (! $user->isDirty('role')) {
            return;
        }

        $oldRole = $user->getOriginal('role');
        $newRole = $user->role;

        Log::info("Zentry: User #{$user->id} role changed from {$oldRole} to {$newRole}");

        match ($oldRole) {
            'organizer' => OrganizerProfile::where('user_id', $user->id)->delete(),
            'coach'     => CoachProfile::where('user_id', $user->id)->delete(),
            'player'    => PlayerProfile::where('user_id', $user->id)->delete(),
            default     => null,
        };

        $this->created($user);
    }

    
//Fires when a user is deleted and Cleans up all profile rows
     
    public function deleted(User $user): void
    {
        OrganizerProfile::where('user_id', $user->id)->delete();
        CoachProfile::where('user_id', $user->id)->delete();
        PlayerProfile::where('user_id', $user->id)->delete();
    }
}