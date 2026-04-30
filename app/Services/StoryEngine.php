<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StoryPage;
use App\Models\User;
use App\Models\UserState;

class StoryEngine
{
    public function getPage(string $slug, ?User $user = null): ?StoryPage
    {
        $page = StoryPage::where('slug', $slug)->first();

        if (!$page) {
            return null;
        }

        if ($user) {
            $state = UserState::forUser($user);
            $page->injectState([
                'player_name' => $user->name,
                'player_level' => $state->level,
                'player_xp' => $state->xp,
            ]);
        }

        return $page;
    }

    public function getMenu(User $user): array
    {
        return match (true) {
            $user->hasRole('team-admin') => $this->adminMenu(),
            $user->hasRole('player') => $this->playerMenu(),
            default => $this->guestMenu(),
        };
    }

    public function processChoice(User $user, string $choiceId): ?StoryPage
    {
        UserState::forUser($user)->recordChoice($choiceId);

        $nextSlug = $this->resolveChoice($choiceId);

        return $nextSlug ? $this->getPage($nextSlug, $user) : null;
    }

    public function recentLore(int $limit = 5): array
    {
        return StoryPage::where('type', 'story')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($page) => [
                'title' => $page->title,
                'slug' => $page->slug,
                'preview' => implode("\n", array_slice($page->content ?? [], 0, 3)),
            ])
            ->toArray();
    }

    private function adminMenu(): array
    {
        return [
            ['key' => '1', 'label' => 'Play - Learning Modules', 'slug' => 'menu-play'],
            ['key' => '2', 'label' => 'Stats - Family Progress', 'slug' => 'stats'],
            ['key' => '3', 'label' => 'Manage - Children & Co-parents', 'slug' => 'manage'],
            ['key' => '4', 'label' => 'Backstory - World Lore', 'slug' => 'backstory'],
            ['key' => '5', 'label' => 'Account - Profile & Settings', 'slug' => 'account'],
        ];
    }

    private function playerMenu(): array
    {
        return [
            ['key' => '1', 'label' => 'Play - Learning Games', 'slug' => 'menu-play'],
            ['key' => '2', 'label' => 'Stats - Your Progress', 'slug' => 'stats'],
            ['key' => '3', 'label' => 'Backstory - World Story', 'slug' => 'backstory'],
            ['key' => '4', 'label' => 'Account - Your Profile', 'slug' => 'account'],
        ];
    }

    private function guestMenu(): array
    {
        return [
            ['key' => '1', 'label' => 'Login', 'slug' => 'login'],
            ['key' => '2', 'label' => 'Register', 'slug' => 'register'],
            ['key' => '3', 'label' => 'About', 'slug' => 'about'],
        ];
    }

    private function resolveChoice(string $choiceId): ?string
    {
        // Simple mapping - can be expanded with database-driven choices
        $map = [
            'play_math' => 'math-start',
            'play_science' => 'science-start',
            'play_language' => 'language-start',
            'play_history' => 'history-start',
            'play_art' => 'art-start',
        ];

        return $map[$choiceId] ?? null;
    }
}
