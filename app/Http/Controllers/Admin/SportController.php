<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SportController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 15, 25]) ? $perPage : 10;

        // Active sports first, archived at the bottom
        $sports = Sport::when($request->search, function ($q) use ($request) {
                $q->where('sport_name', 'like', '%' . $request->search . '%');
            })
            ->withCount('teams', 'tournaments')
            ->orderByDesc('is_active')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.sports.index', compact('sports'));
    }

    public function create(): View
    {
        return view('admin.sports.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'sport_name'          => ['required', 'string', 'unique:sports,sport_name', 'max:100'],
            'min_teams_per_match' => ['required', 'integer', 'min:2'],
            'max_teams_per_match' => ['required', 'integer', 'min:2', 'gte:min_teams_per_match'],
            'description'         => ['nullable', 'string', 'max:500'],
        ]);

        Sport::create([
            'sport_name'          => $request->sport_name,
            'min_teams_per_match' => $request->min_teams_per_match,
            'max_teams_per_match' => $request->max_teams_per_match,
            'description'         => $request->description,
            'is_active'           => true,
        ]);

        return redirect()
            ->route('admin.sports.index')
            ->with('success', 'Sport created successfully.');
    }

    public function edit(Sport $sport): View
    {
        return view('admin.sports.edit', compact('sport'));
    }

    public function update(Request $request, Sport $sport): RedirectResponse
    {
        $request->validate([
            'sport_name'          => ['required', 'string', 'unique:sports,sport_name,' . $sport->id, 'max:100'],
            'min_teams_per_match' => ['required', 'integer', 'min:2'],
            'max_teams_per_match' => ['required', 'integer', 'min:2', 'gte:min_teams_per_match'],
            'description'         => ['nullable', 'string', 'max:500'],
        ]);

        $sport->update([
            'sport_name'          => $request->sport_name,
            'min_teams_per_match' => $request->min_teams_per_match,
            'max_teams_per_match' => $request->max_teams_per_match,
            'description'         => $request->description,
        ]);

        return redirect()
            ->route('admin.sports.index')
            ->with('success', 'Sport updated successfully.');
    }

    
    // Archive 
     
    public function destroy(Sport $sport): RedirectResponse
    {
        $sport->update(['is_active' => false]);

        return redirect()
            ->route('admin.sports.index')
            ->with('success', "\"{$sport->sport_name}\" has been archived.");
    }

    
      //Restore an archived sport.
     
    public function restore(Sport $sport): RedirectResponse
    {
        $sport->update(['is_active' => true]);

        return redirect()
            ->route('admin.sports.index')
            ->with('success', "\"{$sport->sport_name}\" has been restored.");
    }
}