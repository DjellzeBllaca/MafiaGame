<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    const TYPE_DAY = 'Day';
    const TYPE_NIGHT = 'Night';

    protected $fillable = [
        'type', 
        'game_id', 
        'killed',
        'voted_out',
        'saved',
        'investigated',
        'robbed',
    ];

    protected $appends = [
        'user_killed_name',
        'user_voted_out_name',
        'user_saved_name',
        'user_investigated_name',
        'user_robbed_name',
    ];

    protected $with = [
        'userKilled',
        'userVotedOut',
        'userSaved',
        'userInvestigated',
        'userRobbed',
    ];

    public function userKilled()
    {
        return $this->belongsTo(User::class, 'killed');
    }

    public function userVotedOut()
    {
        return $this->belongsTo(User::class, 'voted_out');
    }

    public function userSaved()
    {
        return $this->belongsTo(User::class, 'saved');
    }

    public function userInvestigated()
    {
        return $this->belongsTo(User::class, 'investigated');
    }

    public function userRobbed()
    {
        return $this->belongsTo(User::class, 'robbed');
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function getUserKilledNameAttribute()
    {
        if ($this->userKilled) {
            return $this->userKilled->name;
        }

        return null;
    }

    public function getUserVotedOutNameAttribute()
    {
        if ($this->userVotedOut) {
            return $this->userVotedOut->name;
        }

        return null;
    }

    public function getUserSavedNameAttribute()
    {
        if ($this->userSaved) {
            return $this->userSaved->name;
        }

        return null;
    }

    public function getUserInvestigatedNameAttribute()
    {
        if ($this->userInvestigated) {
            return $this->userInvestigated->name;
        }

        return null;
    }

    public function getUserRobbedNameAttribute()
    {
        if ($this->userRobbed) {
            return $this->userRobbed->name;
        }

        return null;
    }
}
