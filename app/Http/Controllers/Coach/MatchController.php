<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\ZentryMatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();

        $matchQuery = ZentryMatch::whereHas('matchTeams', fn($q) => $q->where('team_id', $team->id))
            ->with(['tournament', 'matchTeams.team', 'matchTeams.result'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('match_date');

        $matches = (clone $matchQuery)->paginate(15)->withQueryString();

        $calendarMonth = Carbon::createFromFormat('Y-m', $request->get('month', now()->format('Y-m')))
            ->startOfMonth();
        $start = $calendarMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $end = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $calendarMatches = (clone $matchQuery)
            ->whereBetween('match_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->get()
            ->groupBy(fn ($match) => $match->match_date->toDateString());

        $calendarGrid = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $cursor->toDateString();
                $week[] = [
                    'date' => $cursor->copy(),
                    'matches' => $calendarMatches->get($dateKey, collect()),
                    'isCurrentMonth' => $cursor->month === $calendarMonth->month,
                ];
                $cursor->addDay();
            }
            $calendarGrid[] = $week;
        }

        return view('coach.matches.index', compact('team', 'matches', 'calendarMonth', 'calendarGrid'));
    }

    public function show(ZentryMatch $match): View
    {
        $team = Team::where('coach_id', auth()->id())->firstOrFail();
        $match->load(['tournament', 'matchTeams.team', 'matchTeams.result']);
        return view('coach.matches.show', compact('match', 'team'));
    }
}
