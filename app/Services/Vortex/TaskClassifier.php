<?php

declare(strict_types=1);

namespace App\Services\Vortex;

use App\Models\PlayerProfile;

class TaskClassifier
{
    /**
     * Classify the intent and determine which model tier to use.
     *
     * This uses a fast heuristic approach first. If uncertain,
     * it can fall back to a cheap LLM classification call.
     */
    public function classify(string $intent, PlayerProfile $profile, array $context = []): string
    {
        // Phase-based overrides
        if ($profile->current_phase === 'recruitment' && $intent === 'narrative') {
            return 'first_contact';
        }

        if ($profile->current_phase === 'faction_assignment' && $intent === 'narrative') {
            return 'faction_assignment';
        }

        // Critical moment detection
        if ($this->isCriticalMoment($profile, $context)) {
            return 'critical_moment';
        }

        // Intent mapping
        return match ($intent) {
            'archive_retrieval', 'archive_render' => 'archive_retrieval',
            'classify', 'intent_detect' => 'intent_classification',
            'simple_chat', 'greeting', 'status_check' => 'simple_response',
            'mission_generate' => 'mission_generation',
            'lore_expand' => 'lore_expansion',
            'blog_generate' => 'blog_draft',
            'grade_report' => 'report_assessment',
            'adaptive_story', 'personalized_narrative' => 'adaptive_narrative',
            'trust_build', 'emotional_moment' => 'trust_dialogue',
            'arc_complete', 'story_climax' => 'arc_climax',
            default => 'simple_response',
        };
    }

    /**
     * Determine if this is a critical narrative moment.
     */
    private function isCriticalMoment(PlayerProfile $profile, array $context): bool
    {
        // Low trust + high emotional stakes
        if ($profile->commander_trust < 0.3 && ($context['emotional_stakes'] ?? false)) {
            return true;
        }

        // First few interactions ever
        if (($context['session_message_count'] ?? 0) < 3) {
            return true;
        }

        // Player is about to complete a phase
        if (($context['phase_progress'] ?? 0) > 0.8) {
            return true;
        }

        // Explicit critical flag from narrative engine
        if (($context['is_critical'] ?? false)) {
            return true;
        }

        return false;
    }

    /**
     * Get the model tier for a given task class.
     */
    public function getTier(string $taskClass): string
    {
        $classes = config('openrouter.task_classes', []);

        return $classes[$taskClass]['tier'] ?? 'economy';
    }

    /**
     * Get the estimated cost for a given task class.
     */
    public function getCost(string $taskClass): float
    {
        $classes = config('openrouter.task_classes', []);

        return $classes[$taskClass]['cost'] ?? 0.001;
    }

    /**
     * Check if the session can afford this task class.
     */
    public function canAfford(string $taskClass, float $sessionSpend, array $tierUsage): bool
    {
        $limits = config('openrouter.limits', []);
        $tier = $this->getTier($taskClass);
        $cost = $this->getCost($taskClass);

        // Hard cost limit
        if ($sessionSpend + $cost > ($limits['max_cost_per_session'] ?? 0.50)) {
            return false;
        }

        // Tier-specific limits
        if ($tier === 'premium' && ($tierUsage['premium'] ?? 0) >= ($limits['max_premium_calls_per_session'] ?? 5)) {
            return false;
        }

        if ($tier === 'frontier' && ($tierUsage['frontier'] ?? 0) >= ($limits['max_frontier_calls_per_session'] ?? 1)) {
            return false;
        }

        return true;
    }
}
