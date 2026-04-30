<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveEntry extends Model
{
    protected $fillable = [
        'slug',
        'type',
        'title',
        'content',
        'choices',
        'conditions',
        'vortex_state_required',
        'narrative_beats',
        'used_by',
        'is_generated',
        'generating_model',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'content' => 'json',
        'choices' => 'json',
        'conditions' => 'json',
        'narrative_beats' => 'json',
        'used_by' => 'json',
        'is_generated' => 'boolean',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    public function markUsedBy(int $userId): void
    {
        $usedBy = $this->used_by ?? [];
        if (!in_array($userId, $usedBy, true)) {
            $usedBy[] = $userId;
            $this->used_by = $usedBy;
        }
        $this->usage_count++;
        $this->last_used_at = now();
        $this->save();
    }

    public function isAvailableFor(PlayerProfile $profile): bool
    {
        $conditions = $this->conditions ?? [];

        if (isset($conditions['min_age']) && $profile->age_at_contact < $conditions['min_age']) {
            return false;
        }

        if (isset($conditions['max_age']) && $profile->age_at_contact > $conditions['max_age']) {
            return false;
        }

        if (isset($conditions['affinity']) && $profile->primary_affinity !== $conditions['affinity']) {
            return false;
        }

        if (isset($conditions['phase']) && $profile->current_phase !== $conditions['phase']) {
            return false;
        }

        $usedBy = $this->used_by ?? [];
        if (in_array($profile->user_id, $usedBy, true)) {
            return false;
        }

        return true;
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForVortexState($query, string $state)
    {
        return $query->where(function ($q) use ($state) {
            $q->where('vortex_state_required', $state)
              ->orWhere('vortex_state_required', 'any');
        });
    }
}
