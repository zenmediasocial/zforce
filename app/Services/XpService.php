<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\UserState;

class XpService
{
    public const LEVEL_THRESHOLDS = [
        0,      // Level 1
        100,    // Level 2
        250,    // Level 3
        500,    // Level 4
        1000,   // Level 5
        2000,   // Level 6
        3500,   // Level 7
        5000,   // Level 8
        7500,   // Level 9
        10000,  // Level 10
    ];

    public function addXp(User $user, int $amount, string $reason): void
    {
        $state = UserState::forUser($user);
        $oldLevel = $this->getLevel($state->xp);
        $state->addXp($amount);
        $newLevel = $state->level;

        Activity::create([
            'user_id' => $user->id,
            'team_id' => $user->current_team_id,
            'type' => 'xp_gain',
            'meta' => ['amount' => $amount, 'reason' => $reason],
        ]);

        if ($newLevel > $oldLevel) {
            event(new \App\Events\UserLeveledUp($user, $newLevel));
        }
    }

    public function getLevel(int $xp): int
    {
        $level = 1;
        foreach (self::LEVEL_THRESHOLDS as $lvl => $threshold) {
            if ($xp >= $threshold) {
                $level = $lvl;
            }
        }

        return $level;
    }

    public function xpToNextLevel(int $xp): int
    {
        $currentLevel = $this->getLevel($xp);
        $nextThreshold = self::LEVEL_THRESHOLDS[$currentLevel + 1] ?? end(self::LEVEL_THRESHOLDS);

        return max(0, $nextThreshold - $xp);
    }
}
