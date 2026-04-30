<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\StoryPage;
use App\Models\UserState;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OffCanvasPanel extends Component
{
    public string $name;
    public bool $isOpen = false;
    public ?string $title = null;
    public array $content = [];

    protected $listeners = ['open-panel' => 'handleOpen'];

    public function mount(string $name): void
    {
        $this->name = $name;
        $this->title = match ($name) {
            'multimedia' => 'MULTIMEDIA ARCHIVE',
            'backstory' => 'WORLD LORE',
            'account' => 'ACCOUNT MANAGEMENT',
            'stats' => 'STATISTICS TERMINAL',
            default => strtoupper($name),
        };
    }

    public function handleOpen(string $panel): void
    {
        if ($panel !== $this->name) {
            $this->isOpen = false;

            return;
        }

        $this->isOpen = true;
        $this->loadContent();
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function loadContent(): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->content = ['Please log in to view this content.'];

            return;
        }

        $this->content = match ($this->name) {
            'stats' => $this->loadStats($user),
            'account' => $this->loadAccount($user),
            'backstory' => $this->loadBackstory(),
            'multimedia' => $this->loadMultimedia(),
            default => ['Content not available.'],
        };
    }

    private function loadStats($user): array
    {
        $state = UserState::forUser($user);
        $activities = $user->activities()->latest()->limit(5)->get();

        $lines = [
            "LEVEL:       {$state->level}",
            "XP:          {$state->xp}",
            "NEXT LEVEL:  {$state->xpToNextLevel()} XP needed",
            '',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '',
            "ACTIVITIES:  {$user->activities()->count()}",
            "STREAK:      {$state->streak_days} days",
            '',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '',
            'RECENT ACTIVITY:',
        ];

        foreach ($activities as $activity) {
            $lines[] = "  [{$activity->created_at->format('Y-m-d')}] {$activity->type}";
        }

        if ($activities->isEmpty()) {
            $lines[] = '  No activity recorded yet.';
        }

        $lines[] = '';

        return $lines;
    }

    private function loadAccount($user): array
    {
        $lines = [
            "NAME:     {$user->name}",
            "EMAIL:    {$user->email}",
            "ROLE:     " . ($user->roles->first()?->name ?? 'none'),
            '',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '',
            'TEAM MEMBERSHIP:',
        ];

        foreach ($user->teams as $team) {
            $lines[] = "  - {$team->display_name}";
        }

        $lines[] = '';

        if ($user->isParent() && $user->children->count() > 0) {
            $lines[] = 'CHILDREN:';
            foreach ($user->children as $child) {
                $lines[] = "  - {$child->name}";
            }
            $lines[] = '';
        }

        return $lines;
    }

    private function loadBackstory(): array
    {
        $pages = StoryPage::where('type', 'story')->limit(5)->get();

        $lines = [
            'WORLD LORE ARCHIVE',
            '',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '',
        ];

        foreach ($pages as $page) {
            $lines[] = $page->title;
            $lines[] = '';
            $content = $page->content ?? [];
            foreach (array_slice($content, 0, 3) as $line) {
                $lines[] = $line;
            }
            $lines[] = '';
            $lines[] = '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━';
            $lines[] = '';
        }

        return $lines;
    }

    private function loadMultimedia(): array
    {
        return [
            'MULTIMEDIA ARCHIVE',
            '',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '',
            ' [1] Introduction Video',
            ' [2] Science Experiments',
            ' [3] Historical Documentaries',
            ' [4] Art Tutorials',
            ' [5] Music & Rhythm',
            '',
            'Select a category to browse.',
            '',
        ];
    }

    public function render()
    {
        return view('livewire.off-canvas-panel');
    }
}
