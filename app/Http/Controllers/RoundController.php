<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Role;
use App\Models\User;
use App\Models\Round;
use App\Models\GameUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\ActionRequest;
use App\Http\Requests\Game\CreateGameRequest;
use Illuminate\Database\Eloquent\Collection;

class RoundController extends Controller
{
    private int $killedId = 0;
    private int $votedId = 0;
    private int $savedId = 0;
    private int $investigatedId = 0;
    private int $robbedId = 0;
    private int $posibleKilledId = 0;
    private int $posibleVotedId = 0;

    public function vote(Game $game, ActionRequest $request)
    {
        $user = auth()->user();
        $selectedId = $request->input('user_id', null);

        $lastRound = $game->rounds()->latest()->first();
        $type = $lastRound ? ($lastRound->type === Round::TYPE_DAY ? Round::TYPE_NIGHT : Round::TYPE_DAY) : Round::TYPE_NIGHT;

        [$aliveUsers, $aliveVillagers, $aliveMafias, $aliveDoctor, $aliveDetective, $aliveThief] = $this->getAliveUsers($game);
        [$aliveUsersCount, $villagersCount, $mafiaCount, $allMafiaCount] = $this->getCounts($aliveUsers, $aliveVillagers, $aliveMafias, $aliveThief);

        if ($type == Round::TYPE_NIGHT) {
            $this->processUserNightAction($user, $selectedId, $mafiaCount);
            $this->processDoctorAction($game, $aliveDoctor, $aliveUsers);
            $this->processMafiaAction($game, $mafiaCount,  $aliveMafias, $aliveVillagers, $aliveThief);
            $this->processDetectiveAction($game, $aliveDetective,  $aliveUsers);
        } else {
            $this->processUsersDayAction($user, $selectedId, $aliveUsers, $aliveUsersCount);
        }

        $round = $this->createRound($game, $type);
        $this->updateGameAndUserStatus($game, $villagersCount, $allMafiaCount);

        return redirect()->route('dashboard');
    }

    /**
     * Get the alive users based on their roles.
     */
    private function getAliveUsers(Game $game): array
    {
        $aliveUsers = $game->users()->where('status', 1)->get();
        $aliveVillagers = $game->villagers()->where('status', 1)->get();
        $aliveMafias = $game->mafias()->where('status', 1)->get();
        $aliveDoctor = $game->doctors()->where('status', 1)->first();
        $aliveDetective = $game->detectives()->where('status', 1)->first();
        $aliveThief = $game->thieves()->where('status', 1)->first();

        return [$aliveUsers, $aliveVillagers, $aliveMafias, $aliveDoctor, $aliveDetective, $aliveThief];
    }

    /**
     * Get the counts of alive users and specific roles.
     */
    private function getCounts(Collection $aliveUsers, Collection $aliveVillagers, Collection $aliveMafias, ?User $aliveThief): array
    {
        $aliveUsersCount = $aliveUsers->count();
        $villagersCount = $aliveVillagers->count();
        $mafiaCount = $aliveMafias->count();
        $allMafiaCount = $aliveThief ? ($mafiaCount + 1) : $mafiaCount;

        return [$aliveUsersCount, $villagersCount, $mafiaCount, $allMafiaCount];
    }

    /**
     * Process the actions for users during the night phase.
     */
    private function processUserNightAction(User $user, ?int $selectedId = 0, int $mafiaCount): void
    {
        if ($user->is_alive) {
            return;
        }

        if ($user->role_title === Role::ROLE_MAFIA) {
            $this->posibleKilledId = $selectedId;
        } elseif ($user->role_title === Role::ROLE_DOCTOR) {
            $this->savedId = $selectedId;
        } elseif ($user->role_title === Role::ROLE_DETECTIVE) {
            $this->investigatedId = $selectedId;
        } elseif ($user->role_title === Role::ROLE_THIEF) {
            if (!$mafiaCount) {
                $this->posibleKilledId = $selectedId;
            } else {
                $this->robbedId = $selectedId;
            }
        }
    }

    /**
     * Process the action of the Doctor during the night phase.
     */
    private function processDoctorAction(Game $game, ?User $aliveDoctor, Collection $aliveUsers): void
    {
        // Doctor can save one person per night
        if (!$this->savedId && $aliveDoctor) {
            $isRobbed = $game->rounds()->where('robbed', $aliveDoctor)->get()->count();
            if (!$isRobbed) {
                $this->savedId = $aliveUsers->shuffle()->take(1)->first()->id;
            }
        }
    }

    /**
     * Process the actions of the Mafia during the night phase.
     */
    private function processMafiaAction(Game $game, int $mafiaCount, Collection $aliveMafias, Collection $aliveVillagers, ?User $aliveThief): void
    {
        // Mafias can kill one person per night
        // Randomly select one villager per each alive mafia
        if (!$this->killedId && $mafiaCount) {
            foreach ($aliveMafias as $aliveMafia) {
                $kills[] = $aliveVillagers->shuffle()->take(1)->first()->id;
            }
        } elseif ($this->killedId && $mafiaCount === 2) {
            $kills[] = $aliveVillagers->shuffle()->take(1)->first()->id;
        }

        // Can only rob 2 times per game
        // If no mafias alive, inherits mafia's role and can kill
        // Can't rob the same user twice
        // Thief can rob one person per night 
        if (!$this->robbedId && $aliveThief) {
            if (!$mafiaCount) {
                $kills[] = $aliveVillagers->shuffle()->take(1)->first()->id;
            } else {
                $robbedUsers = $game->rounds()->whereNotNull('robbed')->pluck('id');
                if ($robbedUsers->count() < 2) {
                    $this->robbedId = $aliveVillagers->whereNotIn('user_id', $robbedUsers)->shuffle()->take(1)->first()->id;
                }
            }
        }

        // Determine which villager to kill
        if (count(array_unique($kills)) === 1) {
            $this->killedId = $kills[0];
        } else {
            $this->killedId = $kills[array_rand($kills)];
        }
    }

    /**
     * Process the action of the Detective during the night phase.
     */
    private function processDetectiveAction(Game $game, ?User $aliveDetective, Collection $aliveUsers): void
    {
        // Detective can investigate one person per night
        if (!$this->investigatedId && $aliveDetective) {
            $isRobbed = $game->rounds()->where('robbed', $aliveDetective)->get()->count();
            if (!$isRobbed) {
                $this->investigatedId = $aliveUsers->shuffle()->take(1)->first()->id;
            }
        }
    }

    /**
     * Process the actions of users during the day phase.
     */
    private function processUsersDayAction(?User $user, ?int $selectedId, Collection $aliveUsers, int $aliveUsersCount): void
    {
        // If the user is alive and on the village team, allow voting
        if ($user->is_alive && $user->team === Role::TEAM_VILLAGE) {
            $votes[] = $selectedId;
        }

        // If no votes from auth user and there are alive users, randomly vote
        if (!isset($votes) && $aliveUsersCount) {
            foreach ($aliveUsers as $aliveUser) {
                $votes[] = $aliveUsers->shuffle()->take(1)->first()->id;
            }
        } elseif (isset($votes)) {
            // If auth user has voted, continue random voting for the rest of the users
            for ($i = 0; $i < $aliveUsersCount - 1; $i++) {
                $votes[] = $aliveUsers->shuffle()->take(1)->first()->id;
            }
        }

        // Count occurrences of each vote
        $countOccurrences = array_count_values($votes);
        $maxCount = max($countOccurrences);

        // Find the most frequently voted user(s)
        $mostFrequentNumbers = array_keys(array_filter($countOccurrences, function ($count) use ($maxCount) {
            return $count === $maxCount;
        }));

        // Set the votedId based on the voting results
        $this->votedId = count($mostFrequentNumbers) > 1
            ? $mostFrequentNumbers[array_rand($mostFrequentNumbers)]
            : $mostFrequentNumbers[0];
    }


    /**
     * Create a new round in the game.
     */
    private function createRound(Game $game, string $type)
    {
        return $game->rounds()->create([
            'type' => $type,
            'killed' => $this->killedId != 0 ? $this->killedId : null,
            'voted_out' => $this->votedId != 0 ? $this->votedId : null,
            'saved' => $this->savedId != 0 ? $this->savedId : null,
            'investigated' => $this->investigatedId != 0 ? $this->investigatedId : null,
            'robbed' => $this->robbedId != 0 ? $this->robbedId : null,
        ]);
    }

    /**
     * Update game and user statuses based on night and day actions.
     */
    private function updateGameAndUserStatus(Game $game, int &$villagersCount, int &$allMafiaCount): void
    {
        $user = GameUser::where('game_id', $game->id);
        if ($this->killedId) {
            $user = $user->where('user_id', $this->killedId)->first();

            if ($this->savedId != $this->killedId) {
                $user->status = 0;
                $villagersCount--;
            }

            if ($allMafiaCount > $villagersCount) {
                $game->winner_team = Game::TEAM_MAFIA;
                $game->save();
            }
            $user->save();
        } else if ($this->votedId) {
            $user = $user->where('user_id', $this->votedId)->first();

            if ($user->role->team === Role::TEAM_VILLAGE) {
                $villagersCount--;
            } else {
                $allMafiaCount--;
            }

            if ($allMafiaCount == 0) {
                $game->winner_team = Game::TEAM_VILLAGE;
                $game->save();
            } elseif ($allMafiaCount > $villagersCount) {
                $game->winner_team = Game::TEAM_MAFIA;
                $game->save();
            }

            $user->status = 0;
            $user->save();
        }
    }
}
