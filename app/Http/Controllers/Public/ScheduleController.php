<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ZentryMatch;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $matches = ZentryMatch::with(['tournament.sport', 'matchTeams.team'])
            ->where('is_active', true)
            ->when($request->tournament_id, fn($q) => $q->where('tournament_id', $request->tournament_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->paginate(20)
            ->withQueryString();

        $tournaments = Tournament::where('is_active', true)->orderBy('tournament_name')->get();

        return view('public.schedule.index', compact('matches', 'tournaments'));
    }
}
