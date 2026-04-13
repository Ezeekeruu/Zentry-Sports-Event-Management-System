<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function show(): View
    {
        $profile = auth()->user()->playerProfile?->load(['team.sport', 'team.coach', 'team.playerProfiles.user']);
        $team    = $profile?->team;

        return view('player.team.show', compact('team'));
    }
}
