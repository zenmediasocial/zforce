<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'temporal_id',
        'age_at_contact',
        'primary_affinity',
        'secondary_affinity',
        'vortex_stability',
        'current_phase',
        'commander_trust',
        'fear_level',
        'detected_struggles',
        'detected_strengths',
        'total_xp',
        'faction_class',
        'completed_transmissions',
        'unlocked_lore',
        'active_mission_id',
        'current_chat_session_id',
        'commander_notes',
    ];

    protected $casts = [
        'vortex_stability' => 'float',
        'commander_trust' => 'float',
        'fear_level' => 'float',
        'detected_struggles' => 'json',
        'detected_strengths' => 'json',
        'completed_transmissions' => 'json',
        'unlocked_lore' => 'json',
        'total_xp' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'user_id', 'user_id');
    }

    public function currentChatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'current_chat_session_id');
    }

    public function addXp(int $amount): void
    {
        $this->total_xp += $amount;
        $this->save();
    }

    public function recordTransmission(string $archiveSlug): void
    {
        $completed = $this->completed_transmissions ?? [];
        if (!in_array($archiveSlug, $completed, true)) {
            $completed[] = $archiveSlug;
            $this->completed_transmissions = $completed;
            $this->save();
        }
    }

    public function adjustTrust(float $delta): void
    {
        $this->commander_trust = max(0.0, min(1.0, $this->commander_trust + $delta));
        $this->save();
    }

    public function adjustStability(float $delta): void
    {
        $this->vortex_stability = max(0.0, min(1.0, $this->vortex_stability + $delta));
        $this->save();
    }

    public function advancePhase(string $newPhase): void
    {
        $this->current_phase = $newPhase;
        $this->save();
    }

    public static function forUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'temporal_id' => 'REC-' . now()->format('Y') . '-' . strtoupper(substr(md5($user->id . now()->timestamp), 0, 8)),
                'vortex_stability' => 0.50,
                'current_phase' => 'recruitment',
                'commander_trust' => 0.00,
                'fear_level' => 0.00,
                'total_xp' => 0,
                'detected_struggles' => [],
                'detected_strengths' => [],
                'completed_transmissions' => [],
                'unlocked_lore' => [],
            ]
        );
    }
}
