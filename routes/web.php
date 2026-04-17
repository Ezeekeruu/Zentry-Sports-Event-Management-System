<?php

use Illuminate\Support\Facades\Route;

// ── Home 
Route::get('/', function () {
    return redirect()->route('login');
});

// ── Auth
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ── Admin 
Route::prefix('admin')->name('admin.')->middleware(['auth', 'prevent-back-history', 'role:admin'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{user}/restore', [\App\Http\Controllers\Admin\UserController::class, 'restore'])->name('users.restore');

    // Sports
    Route::get('sports', [\App\Http\Controllers\Admin\SportController::class, 'index'])->name('sports.index');
    Route::get('sports/create', [\App\Http\Controllers\Admin\SportController::class, 'create'])->name('sports.create');
    Route::post('sports', [\App\Http\Controllers\Admin\SportController::class, 'store'])->name('sports.store');
    Route::get('sports/{sport}/edit', [\App\Http\Controllers\Admin\SportController::class, 'edit'])->name('sports.edit');
    Route::put('sports/{sport}', [\App\Http\Controllers\Admin\SportController::class, 'update'])->name('sports.update');
    Route::delete('sports/{sport}', [\App\Http\Controllers\Admin\SportController::class, 'destroy'])->name('sports.destroy');
    Route::patch('sports/{sport}/restore', [\App\Http\Controllers\Admin\SportController::class, 'restore'])->name('sports.restore');

    // Teams
    Route::get('teams', [\App\Http\Controllers\Admin\TeamController::class, 'index'])->name('teams.index');
    Route::get('teams/create', [\App\Http\Controllers\Admin\TeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [\App\Http\Controllers\Admin\TeamController::class, 'store'])->name('teams.store');
    Route::get('teams/{team}/edit', [\App\Http\Controllers\Admin\TeamController::class, 'edit'])->name('teams.edit');
    Route::put('teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'update'])->name('teams.update');
    Route::delete('teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'destroy'])->name('teams.destroy');
    Route::patch('teams/{team}/restore', [\App\Http\Controllers\Admin\TeamController::class, 'restore'])->name('teams.restore');
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
    Route::patch('tournaments/{tournament}/restore', [\App\Http\Controllers\Admin\TournamentController::class, 'restore'])->name('tournaments.restore');

    // Registrations
    Route::get('registrations', [\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('registrations', [\App\Http\Controllers\Admin\RegistrationController::class, 'store'])->name('registrations.store');
    Route::patch('registrations/{registration}/approve', [\App\Http\Controllers\Admin\RegistrationController::class, 'approve'])->name('registrations.approve');
    Route::patch('registrations/{registration}/reject', [\App\Http\Controllers\Admin\RegistrationController::class, 'reject'])->name('registrations.reject');
    Route::delete('registrations/{registration}', [\App\Http\Controllers\Admin\RegistrationController::class, 'destroy'])->name('registrations.destroy');

    // Matches
    Route::get('matches', [\App\Http\Controllers\Admin\MatchController::class, 'index'])->name('matches.index');
    Route::get('matches/create', [\App\Http\Controllers\Admin\MatchController::class, 'create'])->name('matches.create');
    Route::post('matches', [\App\Http\Controllers\Admin\MatchController::class, 'store'])->name('matches.store');
    Route::get('matches/{match}/edit', [\App\Http\Controllers\Admin\MatchController::class, 'edit'])->name('matches.edit');
    Route::put('matches/{match}', [\App\Http\Controllers\Admin\MatchController::class, 'update'])->name('matches.update');
    Route::get('matches/{match}/winner', [\App\Http\Controllers\Admin\MatchController::class, 'winnerEdit'])->name('matches.winner.edit');
    Route::put('matches/{match}/winner', [\App\Http\Controllers\Admin\MatchController::class, 'winnerUpdate'])->name('matches.winner.update');
    Route::delete('matches/{match}', [\App\Http\Controllers\Admin\MatchController::class, 'destroy'])->name('matches.destroy');
    Route::patch('matches/{match}/restore', [\App\Http\Controllers\Admin\MatchController::class, 'restore'])->name('matches.restore');

    // Results
    Route::get('results', [\App\Http\Controllers\Admin\ResultController::class, 'index'])->name('results.index');
    Route::get('results/{matchTeam}/edit', [\App\Http\Controllers\Admin\ResultController::class, 'edit'])->name('results.edit');
    Route::put('results/{matchTeam}', [\App\Http\Controllers\Admin\ResultController::class, 'update'])->name('results.update');

    // Standings
    Route::get('standings', [\App\Http\Controllers\Admin\StandingController::class, 'index'])->name('standings.index');
});

// ── Organizer 
Route::prefix('organizer')->name('organizer.')->middleware(['auth', 'prevent-back-history', 'role:organizer'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\Organizer\DashboardController::class, 'index'])->name('dashboard');

    Route::get('tournaments', [\App\Http\Controllers\Organizer\TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('tournaments/create', [\App\Http\Controllers\Organizer\TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('tournaments', [\App\Http\Controllers\Organizer\TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('tournaments/{tournament}', [\App\Http\Controllers\Organizer\TournamentController::class, 'show'])->name('tournaments.show');
    Route::get('tournaments/{tournament}/edit', [\App\Http\Controllers\Organizer\TournamentController::class, 'edit'])->name('tournaments.edit');
    Route::put('tournaments/{tournament}', [\App\Http\Controllers\Organizer\TournamentController::class, 'update'])->name('tournaments.update');
    Route::delete('tournaments/{tournament}', [\App\Http\Controllers\Organizer\TournamentController::class, 'destroy'])->name('tournaments.destroy');

    Route::get('registrations', [\App\Http\Controllers\Organizer\RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('registrations', [\App\Http\Controllers\Organizer\RegistrationController::class, 'store'])->name('registrations.store');
    Route::patch('registrations/{registration}/approve', [\App\Http\Controllers\Organizer\RegistrationController::class, 'approve'])->name('registrations.approve');
    Route::patch('registrations/{registration}/reject', [\App\Http\Controllers\Organizer\RegistrationController::class, 'reject'])->name('registrations.reject');

    Route::get('matches', [\App\Http\Controllers\Organizer\MatchController::class, 'index'])->name('matches.index');
    Route::get('matches/create', [\App\Http\Controllers\Organizer\MatchController::class, 'create'])->name('matches.create');
    Route::post('matches', [\App\Http\Controllers\Organizer\MatchController::class, 'store'])->name('matches.store');
    Route::get('matches/{match}/edit', [\App\Http\Controllers\Organizer\MatchController::class, 'edit'])->name('matches.edit');
    Route::put('matches/{match}', [\App\Http\Controllers\Organizer\MatchController::class, 'update'])->name('matches.update');
    Route::get('matches/{match}/winner', [\App\Http\Controllers\Organizer\MatchController::class, 'winnerEdit'])->name('matches.winner.edit');
    Route::put('matches/{match}/winner', [\App\Http\Controllers\Organizer\MatchController::class, 'winnerUpdate'])->name('matches.winner.update');
    Route::delete('matches/{match}', [\App\Http\Controllers\Organizer\MatchController::class, 'destroy'])->name('matches.destroy');

    Route::get('results', [\App\Http\Controllers\Organizer\ResultController::class, 'index'])->name('results.index');
    Route::get('results/{matchTeam}/record', [\App\Http\Controllers\Organizer\ResultController::class, 'edit'])->name('results.edit');
    Route::put('results/{matchTeam}', [\App\Http\Controllers\Organizer\ResultController::class, 'update'])->name('results.update');
});

// ── Coach 
Route::prefix('coach')->name('coach.')->middleware(['auth', 'prevent-back-history', 'role:coach'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\Coach\DashboardController::class, 'index'])->name('dashboard');

    Route::get('team', [\App\Http\Controllers\Coach\TeamController::class, 'show'])->name('team.show');
    Route::get('team/edit', [\App\Http\Controllers\Coach\TeamController::class, 'edit'])->name('team.edit');
    Route::put('team', [\App\Http\Controllers\Coach\TeamController::class, 'update'])->name('team.update');

    Route::get('team/players', [\App\Http\Controllers\Coach\PlayerController::class, 'index'])->name('team.players');
    Route::post('team/players', [\App\Http\Controllers\Coach\PlayerController::class, 'store'])->name('team.players.store');
    Route::delete('team/players/{player}', [\App\Http\Controllers\Coach\PlayerController::class, 'destroy'])->name('team.players.destroy');

    Route::get('matches', [\App\Http\Controllers\Coach\MatchController::class, 'index'])->name('matches.index');
    Route::get('matches/{match}', [\App\Http\Controllers\Coach\MatchController::class, 'show'])->name('matches.show');

    Route::get('results', [\App\Http\Controllers\Coach\ResultController::class, 'index'])->name('results.index');

    Route::get('registrations', [\App\Http\Controllers\Coach\RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('registrations', [\App\Http\Controllers\Coach\RegistrationController::class, 'store'])->name('registrations.store');
});

// ── Player
Route::prefix('player')->name('player.')->middleware(['auth', 'prevent-back-history', 'role:player'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\Player\DashboardController::class, 'index'])->name('dashboard');
    Route::get('team', [\App\Http\Controllers\Player\TeamController::class, 'show'])->name('team.show');
    Route::get('matches', [\App\Http\Controllers\Player\MatchController::class, 'index'])->name('matches.index');
    Route::get('results', [\App\Http\Controllers\Player\ResultController::class, 'index'])->name('results.index');
    Route::get('stats', [\App\Http\Controllers\Player\StatController::class, 'index'])->name('stats.index');

    Route::get('profile/edit', [\App\Http\Controllers\Player\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\Player\ProfileController::class, 'update'])->name('profile.update');
});

// ── Fan / Public 
Route::prefix('public')->name('public.')->group(function () {
    Route::get('tournaments', [\App\Http\Controllers\Public\TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('tournaments/{tournament}', [\App\Http\Controllers\Public\TournamentController::class, 'show'])->name('tournaments.show');
    Route::get('standings', [\App\Http\Controllers\Public\StandingController::class, 'index'])->name('standings.index');
    Route::get('schedule', [\App\Http\Controllers\Public\ScheduleController::class, 'index'])->name('schedule.index');
});