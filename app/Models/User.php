<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'avatar',
        'is_child',
        'parent_id',
        'current_team_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_child' => 'boolean',
        'password' => 'hashed',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guardianships', 'parent_id', 'child_id');
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guardianships', 'child_id', 'parent_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function teamNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(UserState::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievement_user')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function hasAchievement(Achievement $achievement): bool
    {
        return $this->achievements()->where('achievement_id', $achievement->id)->exists();
    }

    public function isParent(): bool
    {
        return !$this->is_child;
    }

    public function isChild(): bool
    {
        return $this->is_child;
    }
}
