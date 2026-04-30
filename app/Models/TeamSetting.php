<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSetting extends Model
{
    protected $fillable = [
        'team_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
