<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'chat_session_id',
        'archive_entry_id',
        'mission_slug',
        'answers',
        'assessment',
        'xp_awarded',
        'status',
        'submitted_at',
        'processed_at',
        'commander_response',
    ];

    protected $casts = [
        'answers' => 'json',
        'assessment' => 'json',
        'xp_awarded' => 'integer',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function archiveEntry(): BelongsTo
    {
        return $this->belongsTo(ArchiveEntry::class);
    }

    public function markProcessing(): void
    {
        $this->status = 'processing';
        $this->save();
    }

    public function markGraded(array $assessment, int $xp, string $commanderResponse): void
    {
        $this->assessment = $assessment;
        $this->xp_awarded = $xp;
        $this->commander_response = $commanderResponse;
        $this->status = 'graded';
        $this->processed_at = now();
        $this->save();
    }

    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'queued');
    }
}
