<?php

namespace App\Http\Controllers;

use PDO;
use App\Models\Game;
use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Round;
use Inertia\Response;
use App\Models\GameUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Game\CreateGameRequest;

class GameController extends Controller
{
    /**
     * Display the dashboard with game information.
     */
    public function index(): Response
    {
        $user = auth()->user();

        // Get the active game with related models (eager loading)
        $activeGame = $user->games()->with([
            'users.roles',
            'rounds.userKilled',
            'rounds.userVotedOut',
            'rounds.userSaved',
            'rounds.userInvestigated',
            'rounds.userRobbed'
        ])->whereNull('winner_team')->latest()->first();

        if ($activeGame) {
            // Determine if it's night based on the latest round's type
            $lastRound = $activeGame->rounds()->latest()->first();
            $isNight = $lastRound ? $lastRound->type == Round::TYPE_DAY : true;
        }

        // Get the last completed game and user's role in that game
        $lastGame = $user->games()->whereNotNull('winner_team')->latest()->first();
        $lastRole = $lastGame ? $lastGame->roles()->where('user_id', $user->id)->first() : null;

        return Inertia::render('Dashboard', [
            'activeGame' => $activeGame,
            'lastGame' => $lastGame,
            'lastRole' => $lastGame ? $lastRole->team : null,
            'players' => $activeGame ? $activeGame->users : null,
            'rounds' => $activeGame ? $activeGame->rounds : null,
            'isNight' => $activeGame ? $isNight : null,
        ]);
    }

    /**
     * Start a new game with the specified title.
     */
    public function start(CreateGameRequest $request):RedirectResponse
    {
        // Validate the incoming request
        $validated = $request->validated();
        $user = auth()->user();

        // Create a new game with the provided title
        $game = $user->games()->create(['title' => $validated['title']]);

        // Get a collection of bots and the user, shuffle them
        $bots = User::whereNull('password')->take(9)->get();
        $players = $bots->merge([$user])->shuffle();
        $roles = Role::all();

        // Role assignments for each role
        $roleAssignments = Role::ROLE_ASSIGNMENTS;

        foreach ($roles as $role) {
            $count = $roleAssignments[$role->title] ?? 0;
            $usersToAssign = $players->take($count);

            foreach ($usersToAssign as $player) {
                $game->users()->attach($player->id, [
                    'role_id' => $role->id, 
                    'status' => 1,
                    'created_at' => now(), 
                    'updated_at' => now()
                ]);

                $players = $players->reject(function ($user) use ($player) {
                    return $user->id === $player->id;
                });
            }
        }

        // Redirect to the dashboard after starting the game
        return redirect()->route('dashboard');
    }
}
