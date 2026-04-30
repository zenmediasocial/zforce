<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'choices',
        'type',
        'required_role',
    ];

    protected $casts = [
        'content' => 'array',
        'choices' => 'array',
    ];

    public function injectState(array $state): void
    {
        $this->content = array_map(
            fn ($line) => str_replace(
                array_map(fn ($k) => "{{{$k}}}", array_keys($state)),
                array_values($state),
                $line
            ),
            $this->content ?? []
        );
    }
}
