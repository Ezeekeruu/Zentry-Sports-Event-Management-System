<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Team;
use App\Models\ZentryMatch;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers        = User::count();
        $activeTournaments = Tournament::where('status', 'ongoing')->count();
        $totalTeams        = Team::count();
        $matchesToday      = ZentryMatch::whereDate('match_date', today())->count();
        $recentTournaments = Tournament::with(['sport', 'organizer'])
                                ->latest()->take(5)->get();
        $recentUsers       = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeTournaments',
            'totalTeams',
            'matchesToday',
            'recentTournaments',
            'recentUsers'
        ));
    }
}