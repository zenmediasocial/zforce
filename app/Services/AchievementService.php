<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;

class AchievementService
{
    public function checkUnlocks(User $user): array
    {
        $unlocked = [];

        foreach (Achievement::all() as $achievement) {
            if ($this->meetsCriteria($user, $achievement) && !$user->hasAchievement($achievement)) {
                $user->achievements()->attach($achievement, ['unlocked_at' => now()]);
                $unlocked[] = $achievement;
            }
        }

        return $unlocked;
    }

    private function meetsCriteria(User $user, Achievement $achievement): bool
    {
        $criteria = $achievement->criteria ?? [];

        // Example criteria checking
        if (isset($criteria['min_activities'])) {
            if ($user->activities()->count() < $criteria['min_activities']) {
                return false;
            }
        }

        if (isset($criteria['min_level'])) {
            $state = \App\Models\UserState::forUser($user);
            if ($state->level < $criteria['min_level']) {
                return false;
            }
        }

        if (isset($criteria['activity_type'])) {
            $count = $user->activities()
                ->where('type', $criteria['activity_type'])
                ->count();
            if ($count < ($criteria['min_count'] ?? 1)) {
                return false;
            }
        }

        return true;
    }
}
