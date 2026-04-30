<?php

declare(strict_types=1);

namespace App\Services\Vortex;

use App\Models\ArchiveEntry;
use App\Models\PlayerProfile;
use Illuminate\Database\Eloquent\Collection;

class ArchiveRepository
{
    /**
     * Find an archive entry matching the player's current state.
     */
    public function findFor(PlayerProfile $profile, string $type, ?string $vortexState = null): ?ArchiveEntry
    {
        $query = ArchiveEntry::forType($type);

        if ($vortexState) {
            $query->forVortexState($vortexState);
        }

        $candidates = $query->get();

        // Filter by player conditions
        $available = $candidates->filter(fn (ArchiveEntry $entry) => $entry->isAvailableFor($profile));

        if ($available->isEmpty()) {
            return null;
        }

        // Prefer least-used entries
        return $available->sortBy('usage_count')->first();
    }

    /**
     * Find a specific archive entry by slug.
     */
    public function findBySlug(string $slug): ?ArchiveEntry
    {
        return ArchiveEntry::where('slug', $slug)->first();
    }

    /**
     * Find the next archive entry based on a choice.
     */
    public function resolveChoice(ArchiveEntry $current, string $choiceKey, PlayerProfile $profile): ?ArchiveEntry
    {
        $choices = $current->choices ?? [];

        foreach ($choices as $choice) {
            if ($choice['key'] === $choiceKey && isset($choice['next_archive'])) {
                $next = $this->findBySlug($choice['next_archive']);

                if ($next && $next->isAvailableFor($profile)) {
                    return $next;
                }
            }
        }

        return null;
    }

    /**
     * Get a fallback entry when nothing else is available.
     */
    public function getFallback(PlayerProfile $profile): ArchiveEntry
    {
        $fallback = $this->findFor($profile, 'transmission', 'any');

        if ($fallback) {
            return $fallback;
        }

        // Ultimate fallback — create a generic transmission
        return new ArchiveEntry([
            'slug' => 'fallback-' . uniqid(),
            'type' => 'transmission',
            'content' => [
                '[SIGNAL FRAGMENT]',
                '',
                '...the vortex is unstable...',
                '...try again when the signal clears...',
                '',
                '[END TRANSMISSION]',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Wait for signal', 'next_archive' => null],
            ],
            'vortex_state_required' => 'any',
        ]);
    }

    /**
     * Store a generated entry in the archive for future use.
     */
    public function storeGenerated(array $data, string $model): ArchiveEntry
    {
        return ArchiveEntry::create([
            ...$data,
            'is_generated' => true,
            'generating_model' => $model,
            'usage_count' => 0,
            'used_by' => [],
        ]);
    }

    /**
     * Get all entries suitable for a blog post extraction.
     */
    public function getNarrativeArc(PlayerProfile $profile, int $limit = 20): Collection
    {
        return ArchiveEntry::whereIn('slug', $profile->completed_transmissions ?? [])
            ->whereNotNull('narrative_beats')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }
}
