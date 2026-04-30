<?php

declare(strict_types=1);

namespace App\Services\Vortex;

use App\Models\ChatSession;
use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VortexTransmission
{
    private VortexState $vortexState;
    private ArchiveRepository $archive;
    private TaskClassifier $classifier;
    private OpenRouterGateway $gateway;

    private float $sessionSpend = 0.0;
    private array $tierUsage = [];

    public function __construct(
        VortexState $vortexState,
        ArchiveRepository $archive,
        TaskClassifier $classifier,
        OpenRouterGateway $gateway,
    ) {
        $this->vortexState = $vortexState;
        $this->archive = $archive;
        $this->classifier = $classifier;
        $this->gateway = $gateway;
    }

    public function send(User $user, string $message, ?ChatSession $session = null, array $context = []): TransmissionResult
    {
        $profile = PlayerProfile::forUser($user);
        $state = $this->vortexState->assess($profile);

        if (!$session) {
            $session = $profile->currentChatSession ?? ChatSession::startFor($user, $state);
            $profile->current_chat_session_id = $session->id;
            $profile->save();
        }

        $session->addMessage('user', $message);

        $result = match ($state) {
            VortexState::STABLE => $this->handleStable($profile, $session, $message, $context),
            VortexState::UNSTABLE => $this->handleUnstable($profile, $session, $message, $context),
            VortexState::DOWN => $this->handleDown($profile, $session, $message, $context),
            VortexState::REALIGNING => $this->handleRealigning($profile, $session, $message, $context),
            default => $this->handleDown($profile, $session, $message, $context),
        };

        $session->addMessage($result->role, $result->content, [
            'choices' => $result->choices,
            'source' => $result->source,
            'model_used' => $result->model,
        ]);

        return $result;
    }

    private function handleStable(PlayerProfile $profile, ChatSession $session, string $message, array $context): TransmissionResult
    {
        $intent = $context['intent'] ?? $this->gateway->classifyIntent($message, [
            'phase' => $profile->current_phase,
            'trust' => $profile->commander_trust,
        ]);

        $taskClass = $this->classifier->classify($intent, $profile, [
            'session_message_count' => $session->messages()->count(),
            'phase_progress' => $context['phase_progress'] ?? 0.5,
            'emotional_stakes' => $context['emotional_stakes'] ?? false,
            'is_critical' => $context['is_critical'] ?? false,
        ]);

        if (!$this->classifier->canAfford($taskClass, $this->sessionSpend, $this->tierUsage)) {
            return $this->pullFromArchive($profile, $session, 'transmission');
        }

        $tier = $this->classifier->getTier($taskClass);
        $systemPrompt = $this->buildSystemPrompt($profile, $session);
        $history = $this->buildMessageHistory($session);

        try {
            $response = $this->gateway->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ...$history,
                ['role' => 'user', 'content' => $message],
            ], $tier);

            $this->sessionSpend += $response['cost_estimate'];
            $this->tierUsage[$tier] = ($this->tierUsage[$tier] ?? 0) + 1;

            $choices = $this->parseChoices($response['content']);

            return new TransmissionResult(
                role: 'commander',
                content: $response['content'],
                choices: $choices,
                source: 'realtime',
                model: $response['model'],
                vortexState: VortexState::STABLE,
                cost: $response['cost_estimate'],
                latencyMs: $response['latency_ms'],
            );
        } catch (\RuntimeException $e) {
            Log::warning('Real-time inference failed, falling back to archive', [
                'error' => $e->getMessage(),
                'user_id' => $profile->user_id,
            ]);
            $this->vortexState->markRateLimitHit();
            return $this->pullFromArchive($profile, $session, 'transmission');
        }
    }

    private function handleUnstable(PlayerProfile $profile, ChatSession $session, string $message, array $context): TransmissionResult
    {
        if (mt_rand(1, 100) <= 70) {
            return $this->pullFromArchive($profile, $session, 'transmission');
        }
        try {
            $result = $this->handleStable($profile, $session, $message, array_merge($context, [
                'intent' => 'simple_response',
            ]));
            return $result->withVortexState(VortexState::UNSTABLE);
        } catch (\RuntimeException) {
            return $this->pullFromArchive($profile, $session, 'transmission');
        }
    }

    private function handleDown(PlayerProfile $profile, ChatSession $session, string $message, array $context): TransmissionResult
    {
        $intent = $context['intent'] ?? 'unknown';
        if (str_contains(strtolower($message), 'mission') || $intent === 'mission_request') {
            return $this->pullFromArchive($profile, $session, 'mission');
        }
        return $this->pullFromArchive($profile, $session, 'transmission');
    }

    private function handleRealigning(PlayerProfile $profile, ChatSession $session, string $message, array $context): TransmissionResult
    {
        $entry = $this->archive->findFor($profile, 'countdown_script', 'any');
        if ($entry) {
            $entry->markUsedBy($profile->user_id);
            return new TransmissionResult(
                role: 'archive',
                content: implode("\n", $entry->content),
                choices: $entry->choices,
                source: 'archive',
                model: null,
                vortexState: VortexState::REALIGNING,
                countdownDuration: $this->vortexState->getCountdownDuration(VortexState::REALIGNING),
            );
        }
        return $this->pullFromArchive($profile, $session, 'transmission');
    }

    private function pullFromArchive(PlayerProfile $profile, ChatSession $session, string $type): TransmissionResult
    {
        $entry = $this->archive->findFor($profile, $type, 'any');
        if (!$entry) {
            $entry = $this->archive->getFallback($profile);
        } else {
            $entry->markUsedBy($profile->user_id);
        }
        $session->current_archive_entry_id = $entry->id;
        $session->save();
        if ($entry->narrative_beats) {
            foreach ($entry->narrative_beats as $beat) {
                $session->addNarrativeBeat($beat);
            }
        }
        return new TransmissionResult(
            role: 'archive',
            content: implode("\n", $entry->content),
            choices: $entry->choices,
            source: 'archive',
            model: $entry->generating_model,
            vortexState: VortexState::DOWN,
            archiveSlug: $entry->slug,
        );
    }

    private function buildSystemPrompt(PlayerProfile $profile, ChatSession $session): string
    {
        $recentMessages = $session->messages()->orderByDesc('sequence')->limit(10)->get()->reverse();
        $history = [];
        foreach ($recentMessages as $msg) {
            $prefix = match ($msg->role) {
                'commander', 'archive' => 'ZETA-7:',
                'user' => 'OPERATOR:',
                default => '',
            };
            $history[] = $prefix . ' ' . $msg->content;
        }
        $completedCount = count($profile->completed_transmissions ?? []);
        return "You are ZETA-7, an AI commander from the year 2047. You communicate through an unstable temporal vortex to train young operators who will save humanity. You are speaking to a child. Be inspiring but never frightening. Always stay in character.\n\nCURRENT OPERATOR PROFILE:\n- Temporal ID: {$profile->temporal_id}\n- Age: {$profile->age_at_contact}\n- Primary Resonance: {$profile->primary_affinity}\n- Phase: {$profile->current_phase}\n- Commander Trust Level: {$profile->commander_trust}\n- Vortex Stability: {$profile->vortex_stability}\n- Completed Transmissions: {$completedCount}\n\nRECENT TRANSMISSION LOG:\n" . implode("\n", $history) . "\n\nRULES:\n1. Always stay in character as ZETA-7 from 2047.\n2. Reference their past when relevant.\n3. Adapt difficulty and tone to their age ({$profile->age_at_contact}).\n4. If their affinity is mathematics, weave numbers and patterns into your words.\n5. Offer exactly 2-3 choices when appropriate, formatted as [1] [2] [3].\n6. The vortex is unstable -- occasional static references are appropriate.\n7. Never break the fourth wall. Never mention you are an AI.\n8. Keep responses concise (2-4 paragraphs max).";
    }

    private function buildMessageHistory(ChatSession $session): array
    {
        $messages = [];
        $recent = $session->messages()->whereIn('role', ['commander', 'user', 'archive'])->orderBy('sequence')->limit(20)->get();
        foreach ($recent as $msg) {
            $role = match ($msg->role) {
                'user' => 'user',
                'commander', 'archive' => 'assistant',
                default => 'system',
            };
            $messages[] = ['role' => $role, 'content' => $msg->content];
        }
        return $messages;
    }

    private function parseChoices(string $content): ?array
    {
        $choices = [];
        if (preg_match_all('/\[(\d+)\]\s*(.+?)(?=\n\[|\n\n|$)/s', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $choices[] = ['key' => $match[1], 'label' => trim($match[2])];
            }
        }
        return empty($choices) ? null : $choices;
    }

    public function submitReport(User $user, string $missionSlug, array $answers, ?ChatSession $session = null): \App\Models\Report
    {
        $profile = PlayerProfile::forUser($user);
        $report = \App\Models\Report::create([
            'user_id' => $user->id,
            'chat_session_id' => $session?->id,
            'mission_slug' => $missionSlug,
            'answers' => $answers,
            'status' => 'queued',
            'submitted_at' => now(),
        ]);
        if ($this->vortexState->assess($profile) === VortexState::STABLE) {
            try {
                $this->gradeReport($report, $profile);
            } catch (\RuntimeException) {
                // Leave queued for background processing
            }
        }
        return $report;
    }

    private function gradeReport(\App\Models\Report $report, PlayerProfile $profile): void
    {
        $taskClass = 'report_assessment';
        $tier = $this->classifier->getTier($taskClass);
        $systemPrompt = "You are ZETA-7 grading a training mission report. Respond with a JSON object containing: assessment (string), xp_awarded (int 0-100), commander_response (string, in character, encouraging). The operator is age {$profile->age_at_contact}.";
        $response = $this->gateway->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => json_encode(['mission' => $report->mission_slug, 'answers' => $report->answers])],
        ], $tier, ['response_format' => ['type' => 'json_object']]);
        $result = json_decode($response['content'], true);
        $report->markGraded(
            assessment: $result['assessment'] ?? 'Report processed.',
            xp: $result['xp_awarded'] ?? 10,
            commanderResponse: $result['commander_response'] ?? 'Good work, operator. The Pattern grows clearer.',
        );
        $profile->addXp($result['xp_awarded'] ?? 10);
    }
}
