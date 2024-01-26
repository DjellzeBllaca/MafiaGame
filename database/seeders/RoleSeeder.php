<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villageType = Role::TEAM_VILLAGE;
        $mafiaType = Role::TEAM_MAFIA;

        // Seed roles
        Role::create([
            'title' => 'Villager',
            'description' => 'Sided with the village. The Villager is the basic building block of the game and an important member of the Village for voting 
                blocks and figuring out who the Mafia is. Wins if all mafia are dead. The Villager votes every day.',
            'team' => $villageType,
            'avatar' => 'storage/images/Villager.png',
        ]);
        Role::create([
            'title' => 'Detective', 
            'description' => 'Sided with the village. The Cop investigates one person each night and receives intel of their alignment (mafia or village).', 
            'team' => $villageType,
            'avatar' => 'storage/images/Detective.png',
        ]);
        Role::create([
            'title' => 'Doctor',
            'description' => 'Sided with the village. The Doctor visits one person each night and protects them from dying that night. That person will not 
                die if they are killed the same night as the Doctor visits. The Doctor is a top-level medic, but is working alone and can only save one 
                person per night. Some say he has a fear of dying and has been known to save himself more than others. What a guy, huh?',
            'team' => $villageType,
            'avatar' => 'storage/images/Doctor.png',
        ]);
        Role::create([
            'title' => 'Mafia',
            'description' => 'Sided with the mafia. Visits and kills one person each night. Wins if mafia outnumber the village. These fellas are a family 
                like you have never seen, and you would not suspect that they stay up all night deciding who`s life to ruin. Don`t get on their bad side 
                or let them know your suspicions. It might be the last thing you do',
            'team' => $mafiaType,
            'avatar' => 'storage/images/Mafia.png',
        ]);
        Role::create(['title' => 'Thief', 
            'description' => 'Sided with the mafia. The Robber is the mafioso who specializes in burglary. He robs quietly and has two attempts to steal an 
                item per game. Can steal the abillity to investigate from the Detective, and the ability to save a life from the Doctor.Those roles will not 
                recover their stolen items for the rest of the game. They will be notified that they were robbed the morning after the robbery.',
            'team' => $mafiaType,
            'avatar' => 'storage/images/Robber.png',
        ]);
    }
}
