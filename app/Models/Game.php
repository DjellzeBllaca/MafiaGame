<?php

namespace App\Models;

use App\Models\User;
use App\Models\Round;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;

    public const TEAM_VILLAGE = 'Village';
    public const TEAM_MAFIA = 'Mafia';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'winner_team',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'game_users')->withPivot('role_id', 'status');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'game_users')->withPivot('user_id', 'status');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function villagers()
    {
        return $this->users()->whereNotIn('role_id', Role::where('team', Role::TEAM_MAFIA)->pluck('id')->toArray());
    }

    public function mafias()
    {
        return $this->users()->whereIn('role_id', Role::where('title', Role::ROLE_MAFIA)->pluck('id')->toArray());
    }

    public function doctors()
    {
        return $this->users()->whereIn('role_id', Role::where('title', Role::ROLE_DOCTOR)->pluck('id')->toArray());
    }

    public function detectives()
    {
        return $this->users()->whereIn('role_id', Role::where('title', Role::ROLE_DETECTIVE)->pluck('id')->toArray());
    }

    public function thieves()
    {
        return $this->users()->whereIn('role_id', Role::where('title', Role::ROLE_THIEF)->pluck('id')->toArray());
    }
}
