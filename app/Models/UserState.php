<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserState extends Model
{
    protected $fillable = [
        'user_id',
        'xp',
        'level',
        'choices_made',
        'unlocked_pages',
        'streak_days',
        'last_activity_date',
    ];

    protected $casts = [
        'xp' => 'integer',
        'level' => 'integer',
        'choices_made' => 'json',
        'unlocked_pages' => 'json',
        'streak_days' => 'integer',
        'last_activity_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function forUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'xp' => 0,
                'level' => 1,
                'choices_made' => [],
                'unlocked_pages' => [],
                'streak_days' => 0,
                'last_activity_date' => null,
            ]
        );
    }

    public function xpToNextLevel(): int
    {
        $thresholds = [0, 100, 250, 500, 1000, 2000, 3500, 5000, 7500, 10000];
        $nextThreshold = $thresholds[$this->level] ?? end($thresholds);

        return max(0, $nextThreshold - $this->xp);
    }

    public function recordChoice(string $choiceId): void
    {
        $choices = $this->choices_made ?? [];
        $choices[] = $choiceId;
        $this->choices_made = $choices;
        $this->save();
    }

    public function addXp(int $amount): void
    {
        $this->xp += $amount;
        $this->checkLevelUp();
        $this->save();
    }

    private function checkLevelUp(): void
    {
        $thresholds = [0, 100, 250, 500, 1000, 2000, 3500, 5000, 7500, 10000];
        $newLevel = 1;

        foreach ($thresholds as $level => $threshold) {
            if ($this->xp >= $threshold) {
                $newLevel = $level;
            }
        }

        if ($newLevel > $this->level) {
            $this->level = $newLevel;
        }
    }
}
