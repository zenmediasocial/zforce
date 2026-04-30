<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'personal_team',
    ];

    protected $casts = [
        'personal_team' => 'boolean',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(TeamSetting::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = $this->settings()->where('key', $key)->first();

        return $setting ? json_decode($setting->value, true) : $default;
    }

    public function setSetting(string $key, mixed $value): void
    {
        $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value)]
        );
    }
}
