<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/dashboard', [GameController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    // Start a new game route
    Route::post('/game', [GameController::class, 'start'])->name('game.start');

    // Perform action in a game round route
    Route::post('/game/{game}/round', [RoundController::class, 'vote'])->name('round.action');
});

// Authentication routes
require __DIR__.'/auth.php';
