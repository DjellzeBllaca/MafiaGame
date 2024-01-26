<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameUser extends Model
{
    use HasFactory;

    const STATUS_ALIVE = 'alive';
    const STATUS_DEAD = 'dead';

    protected $fillable = [
        'game_id',
        'user_id',
        'role_id',
        'status',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function hasRole($roleTitle)
    {
        return $this->role->title === $roleTitle;
    }

    public function isTeam($team)
    {
        return $this->role->team === $team;
    }
}
