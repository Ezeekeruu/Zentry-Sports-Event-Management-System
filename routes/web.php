<?php

use Illuminate\Support\Facades\Route;

// ── Home ──────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ── Auth routes ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

    Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Sports
    Route::get('sports', [\App\Http\Controllers\Admin\SportController::class, 'index'])->name('sports.index');
    Route::get('sports/create', [\App\Http\Controllers\Admin\SportController::class, 'create'])->name('sports.create');
    Route::post('sports', [\App\Http\Controllers\Admin\SportController::class, 'store'])->name('sports.store');
    Route::get('sports/{sport}/edit', [\App\Http\Controllers\Admin\SportController::class, 'edit'])->name('sports.edit');
    Route::put('sports/{sport}', [\App\Http\Controllers\Admin\SportController::class, 'update'])->name('sports.update');
    Route::delete('sports/{sport}', [\App\Http\Controllers\Admin\SportController::class, 'destroy'])->name('sports.destroy');

    // Teams
    Route::get('teams', [\App\Http\Controllers\Admin\TeamController::class, 'index'])->name('teams.index');
    Route::get('teams/create', [\App\Http\Controllers\Admin\TeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [\App\Http\Controllers\Admin\TeamController::class, 'store'])->name('teams.store');
    Route::get('teams/{team}/edit', [\App\Http\Controllers\Admin\TeamController::class, 'edit'])->name('teams.edit');
    Route::put('teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'update'])->name('teams.update');
    Route::delete('teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'destroy'])->name('teams.destroy');
    Route::get('teams/{team}/players', [\App\Http\Controllers\Admin\TeamController::class, 'players'])->name('teams.players');
    Route::post('teams/{team}/players', [\App\Http\Controllers\Admin\TeamController::class, 'addPlayer'])->name('teams.players.add');
    Route::delete('teams/{team}/players/{player}', [\App\Http\Controllers\Admin\TeamController::class, 'removePlayer'])->name('teams.players.remove');

    // Tournaments
    Route::get('tournaments', [\App\Http\Controllers\Admin\TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('tournaments/create', [\App\Http\Controllers\Admin\TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('tournaments', [\App\Http\Controllers\Admin\TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('tournaments/{tournament}', [\App\Http\Controllers\Admin\TournamentController::class, 'show'])->name('tournaments.show');
    Route::get('tournaments/{tournament}/edit', [\App\Http\Controllers\Admin\TournamentController::class, 'edit'])->name('tournaments.edit');
    Route::put('tournaments/{tournament}', [\App\Http\Controllers\Admin\TournamentController::class, 'update'])->name('tournaments.update');
    Route::delete('tournaments/{tournament}', [\App\Http\Controllers\Admin\TournamentController::class, 'destroy'])->name('tournaments.destroy');

    // Matches
    Route::get('matches', [\App\Http\Controllers\Admin\MatchController::class, 'index'])->name('matches.index');
    Route::get('matches/create', [\App\Http\Controllers\Admin\MatchController::class, 'create'])->name('matches.create');
    Route::post('matches', [\App\Http\Controllers\Admin\MatchController::class, 'store'])->name('matches.store');
    Route::get('matches/{match}/edit', [\App\Http\Controllers\Admin\MatchController::class, 'edit'])->name('matches.edit');
    Route::put('matches/{match}', [\App\Http\Controllers\Admin\MatchController::class, 'update'])->name('matches.update');
    Route::delete('matches/{match}', [\App\Http\Controllers\Admin\MatchController::class, 'destroy'])->name('matches.destroy');

    // Results
    Route::get('results', [\App\Http\Controllers\Admin\ResultController::class, 'index'])->name('results.index');
    Route::get('results/{matchTeam}/edit', [\App\Http\Controllers\Admin\ResultController::class, 'edit'])->name('results.edit');
    Route::put('results/{matchTeam}', [\App\Http\Controllers\Admin\ResultController::class, 'update'])->name('results.update');

    // Standings
    Route::get('standings', [\App\Http\Controllers\Admin\StandingController::class, 'index'])->name('standings.index');

    // Registrations
    Route::get('registrations', [\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('registrations', [\App\Http\Controllers\Admin\RegistrationController::class, 'store'])->name('registrations.store');
    Route::patch('registrations/{registration}/approve', [\App\Http\Controllers\Admin\RegistrationController::class, 'approve'])->name('registrations.approve');
    Route::patch('registrations/{registration}/reject', [\App\Http\Controllers\Admin\RegistrationController::class, 'reject'])->name('registrations.reject');
    Route::delete('registrations/{registration}', [\App\Http\Controllers\Admin\RegistrationController::class, 'destroy'])->name('registrations.destroy');
});

// ── Organizer ─────────────────────────────────────────────────────────────────
Route::prefix('organizer')->name('organizer.')->middleware(['auth', 'role:organizer'])->group(function () {
    Route::get('tournaments', function () { return 'Organizer Tournaments — Phase 3 coming soon'; })->name('tournaments.index');
    Route::get('registrations', [\App\Http\Controllers\Organizer\RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('registrations', [\App\Http\Controllers\Organizer\RegistrationController::class, 'store'])->name('registrations.store');
    Route::patch('registrations/{registration}/approve', [\App\Http\Controllers\Organizer\RegistrationController::class, 'approve'])->name('registrations.approve');
    Route::patch('registrations/{registration}/reject', [\App\Http\Controllers\Organizer\RegistrationController::class, 'reject'])->name('registrations.reject');
});

// ── Coach ─────────────────────────────────────────────────────────────────────
Route::prefix('coach')->name('coach.')->middleware(['auth', 'role:coach'])->group(function () {
    Route::get('team', function () { return 'Coach Team — Phase 4 coming soon'; })->name('team.show');
});

// ── Player ────────────────────────────────────────────────────────────────────
Route::prefix('player')->name('player.')->middleware(['auth', 'role:player'])->group(function () {
    Route::get('dashboard', function () { return 'Player Dashboard — Phase 5 coming soon'; })->name('dashboard');
});

// ── Fan / Public ──────────────────────────────────────────────────────────────
Route::prefix('public')->name('public.')->group(function () {
    Route::get('tournaments', function () { return 'Public Tournaments — Phase 5 coming soon'; })->name('tournaments.index');
});