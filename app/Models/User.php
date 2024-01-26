<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Game;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nickname',
        'email',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'team',
        'role_title',
        'is_alive',
    ];

    public function games()
    {
        return $this->hasMany(Game::class, 'created_by');
    }

    public function userGames()
    {
        return $this->belongsToMany(Game::class, 'game_users')->withPivot('role_id', 'status');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'game_users')->withPivot('game_id', 'status');
    }

    public function getTeamAttribute()
    {
        $activeGame = $this->userGames()->whereNull('winner_team')->first();

        if ($activeGame) {
            $role = $this->roles()->where('game_id', $activeGame->id)->first();
            return $role->team;
        }

        return null;
    }

    public function getRoleTitleAttribute()
    {
        $activeGame = $this->userGames()->whereNull('winner_team')->first();

        if ($activeGame) {
            $role = $this->roles()->where('game_id', $activeGame->id)->first();
            return $role->title;
        }

        return null;
    }

    public function getIsAliveAttribute()
{
    $activeGame = $this->userGames()->whereNull('winner_team')->first();

    if ($activeGame) {
        $userInGame = $activeGame->users()
            ->where('user_id', $this->id)
            ->where('status', 1)
            ->first();

        return $userInGame !== null;
    }

    return false;
}
}
