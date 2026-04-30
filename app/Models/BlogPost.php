<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'author_id',
        'author_name',
        'author_avatar',
        'category',
        'tags',
        'narrative_beats',
        'story_arc',
        'featured_image',
        'generating_model',
        'status',
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'tags' => 'json',
        'narrative_beats' => 'json',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at !== null && $this->published_at <= now();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function publish(): void
    {
        $this->status = 'published';
        $this->published_at = now();
        $this->save();
    }

    public function unpublish(): void
    {
        $this->status = 'draft';
        $this->published_at = null;
        $this->save();
    }

    public function archive(): void
    {
        $this->status = 'archived';
        $this->save();
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function createFromN8n(array $data, ?User $author = null): self
    {
        $slug = Str::slug($data['title'] . '-' . uniqid());

        return self::create([
            'slug' => $slug,
            'title' => $data['title'],
            'excerpt' => Str::limit(strip_tags($data['content']), 200),
            'content' => $data['content'],
            'author_id' => $author?->id,
            'author_name' => $author?->name ?? 'ZETA-7',
            'category' => $data['story_arc'] ?? 'chronicle',
            'tags' => $data['narrative_beats'] ?? [],
            'narrative_beats' => $data['narrative_beats'] ?? [],
            'story_arc' => $data['story_arc'] ?? null,
            'generating_model' => $data['generated_by'] ?? null,
            'status' => 'draft',
        ]);
    }
}
