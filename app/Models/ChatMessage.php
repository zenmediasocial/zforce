<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_session_id',
        'role',
        'content',
        'choices',
        'selected_choice',
        'narrative_beats',
        'source',
        'model_used',
        'metadata',
        'sequence',
    ];

    protected $casts = [
        'choices' => 'json',
        'narrative_beats' => 'json',
        'metadata' => 'json',
        'sequence' => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }

    public function isFromCommander(): bool
    {
        return $this->role === 'commander';
    }

    public function isFromUser(): bool
    {
        return $this->role === 'user';
    }

    public function isFromArchive(): bool
    {
        return $this->role === 'archive';
    }

    public function hasChoices(): bool
    {
        return !empty($this->choices);
    }
}
