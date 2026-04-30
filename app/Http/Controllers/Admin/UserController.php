<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [5, 10, 15, 25, 50]) ? $perPage : 15;

        $users = User::when($request->search, function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('is_active')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $sports = Sport::where('is_active', true)->orderBy('sport_name')->get();
        return view('admin.users.create', compact('sports'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'role'       => ['required', 'in:admin,organizer,coach,player,fan'],
            'sport_id'   => ['nullable', 'exists:sports,id'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'is_active'  => true,
        ]);

        if ($user->role === 'player' && $request->filled('sport_id')) {
            $user->playerProfile()->update(['sport_id' => $request->sport_id]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $sports = Sport::where('is_active', true)->orderBy('sport_name')->get();
        return view('admin.users.edit', compact('user', 'sports'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'       => ['required', 'in:admin,organizer,coach,player,fan'],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
            'sport_id'   => ['nullable', 'exists:sports,id'],
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'role'       => $request->role,
            'is_active'  => $request->boolean('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($user->role === 'player') {
            $user->refresh();
            $user->playerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['sport_id' => $request->filled('sport_id') ? $request->sport_id : null]
            );
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot archive your own account.');
        }
        $user->update(['is_active' => false]);
        return redirect()->route('admin.users.index')->with('success', "{$user->full_name} has been archived.");
    }

    public function restore(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);
        return redirect()->route('admin.users.index')->with('success', "{$user->full_name} has been restored.");
    }
}