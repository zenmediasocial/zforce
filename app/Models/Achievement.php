<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'criteria',
        'xp_reward',
    ];

    protected $casts = [
        'criteria' => 'json',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'achievement_user')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
