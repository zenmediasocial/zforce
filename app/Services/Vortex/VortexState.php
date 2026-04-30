<?php

declare(strict_types=1);

namespace App\Services\Vortex;

use App\Models\PlayerProfile;

class VortexState
{
    public const STABLE = 'stable';
    public const UNSTABLE = 'unstable';
    public const DOWN = 'down';
    public const REALIGNING = 'realigning';

    private array $queueDepth = [];
    private bool $rateLimitHit = false;
    private int $totalRequests = 0;

    /**
     * Assess the current vortex state for a player.
     *
     * The vortex is unstable by design. Even when systems are healthy,
     * we sometimes report unstable to maintain the lore.
     */
    public function assess(PlayerProfile $profile): string
    {
        // Base state from system health
        $systemState = $this->assessSystemHealth();

        // Player's vortex stability modifies the state
        $stability = $profile->vortex_stability;

        // Roll for random instability (the vortex is unpredictable)
        $chaosRoll = mt_rand(1, 100);

        // Higher stability = less chaos
        $chaosThreshold = max(5, 50 - (int) ($stability * 40));

        if ($chaosRoll <= $chaosThreshold) {
            // Random instability overrides system state
            return $this->degradeState($systemState);
        }

        // Player stability can improve a down vortex
        if ($systemState === self::DOWN && $stability > 0.7) {
            return self::UNSTABLE;
        }

        return $systemState;
    }

    /**
     * Check actual system health (rate limits, queue depth).
     */
    private function assessSystemHealth(): string
    {
        if ($this->rateLimitHit) {
            return self::DOWN;
        }

        $queueDepth = $this->getQueueDepth();

        if ($queueDepth > 10) {
            return self::DOWN;
        }

        if ($queueDepth > 3) {
            return self::UNSTABLE;
        }

        return self::STABLE;
    }

    /**
     * Degrade a stable state to simulate vortex instability.
     */
    private function degradeState(string $state): string
    {
        return match ($state) {
            self::STABLE => self::UNSTABLE,
            self::UNSTABLE => self::DOWN,
            self::DOWN => self::DOWN,
            default => self::UNSTABLE,
        };
    }

    /**
     * Mark that a rate limit has been hit.
     */
    public function markRateLimitHit(): void
    {
        $this->rateLimitHit = true;
    }

    /**
     * Clear rate limit status.
     */
    public function clearRateLimit(): void
    {
        $this->rateLimitHit = false;
    }

    /**
     * Set queue depth for a specific channel.
     */
    public function setQueueDepth(string $channel, int $depth): void
    {
        $this->queueDepth[$channel] = $depth;
    }

    /**
     * Get total queue depth across all channels.
     */
    private function getQueueDepth(): int
    {
        return array_sum($this->queueDepth);
    }

    /**
     * Record a request (for rate tracking).
     */
    public function recordRequest(): void
    {
        $this->totalRequests++;
    }

    /**
     * Get a human-readable vortex status message.
     */
    public function getStatusMessage(string $state): string
    {
        return match ($state) {
            self::STABLE => '[VORTEX LOCKED] Signal is clear.',
            self::UNSTABLE => '[VORTEX UNSTABLE] Signal fragmenting...',
            self::DOWN => '[VORTEX DOWN] Pulling from Archive...',
            self::REALIGNING => '[VORTEX REALIGNING] Stand by...',
            default => '[VORTEX STATUS UNKNOWN]',
        };
    }

    /**
     * Get the countdown duration for a given state.
     */
    public function getCountdownDuration(string $state): int
    {
        return match ($state) {
            self::STABLE => 0,
            self::UNSTABLE => mt_rand(3, 5),
            self::DOWN => mt_rand(5, 8),
            self::REALIGNING => mt_rand(4, 6),
            default => 5,
        };
    }
}
