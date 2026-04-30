<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo parent
        $parent = User::create([
            'name' => 'Demo Parent',
            'email' => 'parent@example.com',
            'password' => Hash::make('password'),
            'date_of_birth' => '1985-06-15',
            'is_child' => false,
        ]);

        // Create personal team
        $team = Team::create([
            'name' => 'Demo Family',
            'display_name' => 'Demo Family',
            'description' => 'A demonstration family team',
            'personal_team' => true,
        ]);

        $parent->teams()->attach($team);
        $parent->current_team_id = $team->id;
        $parent->save();
        $parent->assignRole('team-admin');

        // Create demo child
        $child = User::create([
            'name' => 'Demo Child',
            'email' => 'child_' . uniqid() . '@placeholder.local',
            'password' => Hash::make(Str()->random(32)),
            'date_of_birth' => '2012-03-20',
            'is_child' => true,
            'parent_id' => $parent->id,
        ]);

        $child->teams()->attach($team);
        $child->current_team_id = $team->id;
        $child->save();
        $child->assignRole('player');

        // Create guardianship
        \App\Models\Guardianship::create([
            'parent_id' => $parent->id,
            'child_id' => $child->id,
            'is_primary' => true,
        ]);

        // Create user state for child
        \App\Models\UserState::create([
            'user_id' => $child->id,
            'xp' => 150,
            'level' => 2,
            'choices_made' => [],
            'unlocked_pages' => ['welcome', 'menu-play'],
            'streak_days' => 3,
            'last_activity_date' => now(),
        ]);

        // Create some activities
        \App\Models\Activity::create([
            'user_id' => $child->id,
            'team_id' => $team->id,
            'type' => 'reading',
            'meta' => ['module' => 'science', 'duration' => 15],
        ]);

        \App\Models\Activity::create([
            'user_id' => $child->id,
            'team_id' => $team->id,
            'type' => 'assessment',
            'meta' => ['module' => 'mathematics', 'score' => 85],
        ]);

        \App\Models\Activity::create([
            'user_id' => $child->id,
            'team_id' => $team->id,
            'type' => 'achievement',
            'meta' => ['achievement' => 'first_steps'],
        ]);
    }
}
