<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const TEAM_VILLAGE = 'Village';
    public const TEAM_MAFIA = 'Mafia';

    public const ROLE_VILLAGER = 'Villager';
    public const ROLE_DETECTIVE = 'Detective';
    public const ROLE_DOCTOR = 'Doctor';
    public const ROLE_MAFIA = 'Mafia';
    public const ROLE_THIEF = 'Thief';

    public const ROLE_ASSIGNMENTS = [
        self::ROLE_VILLAGER => 5,
        self::ROLE_DOCTOR => 1,
        self::ROLE_DETECTIVE => 1,
        self::ROLE_MAFIA => 2,
        self::ROLE_THIEF => 1,
    ];

    protected $fillable = ['title', 'description', 'team', 'avatar'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'game_users')->withPivot('game_id', 'status');
    }
}
