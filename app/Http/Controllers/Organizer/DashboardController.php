<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Registration;
use App\Models\ZentryMatch;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $organizer = auth()->user();

        $myTournaments      = Tournament::where('organizer_id', $organizer->id)->count();
        $pendingRegistrations = Registration::whereHas('tournament', fn($q) => $q->where('organizer_id', $organizer->id))
            ->where('status', 'pending')->count();
        $ongoingTournaments = Tournament::where('organizer_id', $organizer->id)->where('status', 'ongoing')->count();
        $recentTournaments  = Tournament::where('organizer_id', $organizer->id)->with('sport')->latest()->take(5)->get();

        return view('organizer.dashboard', compact(
            'myTournaments', 'pendingRegistrations', 'ongoingTournaments', 'recentTournaments'
        ));
    }
}
