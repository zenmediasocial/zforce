<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ArchiveEntry;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * The public landing page for zforce.army
     */
    public function landing()
    {
        $latestChronicles = BlogPost::published()
            ->byCategory('chronicle')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $featuredMissions = ArchiveEntry::forType('mission')
            ->orderByDesc('usage_count')
            ->limit(4)
            ->get();

        $stats = [
            'operators_recruited' => \App\Models\PlayerProfile::count(),
            'missions_completed' => \App\Models\Report::where('status', 'graded')->count(),
            'transmissions_sent' => \App\Models\ChatMessage::count(),
            'chronicles_published' => BlogPost::published()->count(),
        ];

        return view('landing', compact('latestChronicles', 'featuredMissions', 'stats'));
    }

    /**
     * The Chronicles (blog) listing page
     */
    public function chronicles(Request $request)
    {
        $posts = BlogPost::published()
            ->byCategory('chronicle')
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('chronicles.index', compact('posts'));
    }

    /**
     * Single chronicle/blog post
     */
    public function chronicle(string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $post->incrementViews();

        $related = BlogPost::published()
            ->byCategory($post->category)
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get();

        return view('chronicles.show', compact('post', 'related'));
    }

    /**
     * The Mission Library — public view of Archive missions
     */
    public function missions()
    {
        $missions = ArchiveEntry::forType('mission')
            ->orderByDesc('usage_count')
            ->paginate(12);

        return view('missions.index', compact('missions'));
    }

    /**
     * Single mission preview (no answers, just the story)
     */
    public function mission(string $slug)
    {
        $mission = ArchiveEntry::where('slug', $slug)
            ->where('type', 'mission')
            ->firstOrFail();

        return view('missions.show', compact('mission'));
    }

    /**
     * The Lore page — public backstory
     */
    public function lore()
    {
        $entries = ArchiveEntry::forType('transmission')
            ->whereJsonContains('narrative_beats', 'war_explained')
            ->orWhereJsonContains('narrative_beats', 'pattern_introduced')
            ->get();

        return view('lore', compact('entries'));
    }

    /**
     * About page
     */
    public function about()
    {
        return view('about');
    }
}
