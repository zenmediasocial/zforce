<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'vortex_state_at_start',
        'current_archive_entry_id',
        'choices_made',
        'narrative_beats',
        'context_summary',
        'completed_at',
    ];

    protected $casts = [
        'choices_made' => 'json',
        'narrative_beats' => 'json',
        'context_summary' => 'json',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('sequence');
    }

    public function currentArchiveEntry(): BelongsTo
    {
        return $this->belongsTo(ArchiveEntry::class, 'current_archive_entry_id');
    }

    public function addMessage(string $role, string $content, array $metadata = []): ChatMessage
    {
        $sequence = $this->messages()->max('sequence') ?? 0;

        return $this->messages()->create([
            'role' => $role,
            'content' => $content,
            'sequence' => $sequence + 1,
            ...$metadata,
        ]);
    }

    public function recordChoice(string $archiveSlug, string $choiceKey): void
    {
        $choices = $this->choices_made ?? [];
        $choices[] = [
            'archive_slug' => $archiveSlug,
            'choice_key' => $choiceKey,
            'timestamp' => now()->toIso8601String(),
        ];
        $this->choices_made = $choices;
        $this->save();
    }

    public function addNarrativeBeat(string $beat): void
    {
        $beats = $this->narrative_beats ?? [];
        $beats[] = $beat;
        $this->narrative_beats = $beats;
        $this->save();
    }

    public function complete(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public static function startFor(User $user, string $vortexState): self
    {
        return self::create([
            'user_id' => $user->id,
            'status' => 'active',
            'vortex_state_at_start' => $vortexState,
            'choices_made' => [],
            'narrative_beats' => [],
        ]);
    }
}
