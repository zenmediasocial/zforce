<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Vortex\ArchiveRepository;
use App\Services\Vortex\OpenRouterGateway;
use App\Services\Vortex\TaskClassifier;
use App\Services\Vortex\TransmissionResult;
use App\Services\Vortex\VortexState;
use App\Services\Vortex\VortexTransmission;
use Illuminate\Console\Command;

class VortexTest extends Command
{
    protected $signature = 'vortex:test {user_id?} {--archive-only : Skip LLM calls}';
    protected $description = 'Test the Vortex temporal transmission pipeline';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║       ZFORCE VORTEX PIPELINE TEST            ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->newLine();

        // Get or create test user
        $userId = $this->argument('user_id');
        if ($userId) {
            $user = User::findOrFail($userId);
        } else {
            $user = User::first();
            if (!$user) {
                $this->error('No users found. Run db:seed first.');
                return 1;
            }
        }

        $this->info("Operator: {$user->name} (ID: {$user->id})");
        $this->newLine();

        // Test 1: Archive Repository
        $this->info('TEST 1: Archive Repository');
        $this->info('──────────────────────────────────────────────');
        $archive = new ArchiveRepository();
        $profile = \App\Models\PlayerProfile::forUser($user);
        $profile->current_phase = 'recruitment';
        $profile->age_at_contact = 9;
        $profile->primary_affinity = 'mathematics';
        $profile->save();

        $entry = $archive->findFor($profile, 'transmission', 'any');
        if ($entry) {
            $this->info("Found archive entry: {$entry->slug}");
            $this->line(implode("\n", $entry->content));
            $this->newLine();
        } else {
            $this->error('No archive entry found');
            return 1;
        }

        // Test 2: Vortex State Assessment
        $this->info('TEST 2: Vortex State Assessment');
        $this->info('──────────────────────────────────────────────');
        $vortexState = new VortexState();
        for ($i = 0; $i < 5; $i++) {
            $state = $vortexState->assess($profile);
            $msg = $vortexState->getStatusMessage($state);
            $this->line("  Roll {$i}: {$state} — {$msg}");
        }
        $this->newLine();

        // Test 3: Task Classification
        $this->info('TEST 3: Task Classification');
        $this->info('──────────────────────────────────────────────');
        $classifier = new TaskClassifier();
        $testIntents = ['greeting', 'mission_request', 'first_contact', 'arc_climax'];
        foreach ($testIntents as $intent) {
            $taskClass = $classifier->classify($intent, $profile);
            $tier = $classifier->getTier($taskClass);
            $cost = $classifier->getCost($taskClass);
            $this->line("  Intent: {$intent} → Task: {$taskClass} → Tier: {$tier} (~\${$cost})");
        }
        $this->newLine();

        // Test 4: Full Transmission Pipeline (Archive Only)
        $this->info('TEST 4: Full Transmission Pipeline');
        $this->info('──────────────────────────────────────────────');

        $gateway = new OpenRouterGateway();
        $transmission = new VortexTransmission($vortexState, $archive, $classifier, $gateway);

        $messages = ['connect', 'Tell me about the war', 'I am 9 years old'];

        foreach ($messages as $message) {
            $this->line("  > {$message}");
            $result = $transmission->send($user, $message);

            $icon = match ($result->vortexState) {
                VortexState::STABLE => '✓',
                VortexState::UNSTABLE => '~',
                VortexState::DOWN => '▾',
                VortexState::REALIGNING => '◈',
                default => '?',
            };

            $this->line("  {$icon} [{$result->vortexState}] Source: {$result->source}");
            if ($result->model) {
                $this->line("    Model: {$result->model}");
            }
            if ($result->cost) {
                $this->line("    Cost: \${$result->cost}");
            }

            // Show first few lines of response
            $lines = explode("\n", $result->content);
            foreach (array_slice($lines, 0, 4) as $line) {
                if (trim($line)) {
                    $this->line("    {$line}");
                }
            }
            if (count($lines) > 4) {
                $this->line("    ... (" . (count($lines) - 4) . " more lines)");
            }

            if ($result->choices) {
                foreach ($result->choices as $choice) {
                    $this->line("    [{$choice['key']}] {$choice['label']}");
                }
            }

            $this->newLine();
        }

        // Test 5: OpenRouter Config Check
        $this->info('TEST 5: OpenRouter Configuration');
        $this->info('──────────────────────────────────────────────');
        $apiKey = config('openrouter.api_key');
        if ($apiKey) {
            $this->info('  API Key: ' . substr($apiKey, 0, 8) . '...' . substr($apiKey, -4));
        } else {
            $this->warn('  API Key: NOT SET (set OPENROUTER_API_KEY in .env)');
        }
        $this->line('  Base URL: ' . config('openrouter.base_url'));
        foreach (config('openrouter.models', []) as $tier => $model) {
            $this->line("  {$tier}: {$model}");
        }
        $this->newLine();

        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║           ALL TESTS COMPLETED                ║');
        $this->info('╚══════════════════════════════════════════════╝');

        return 0;
    }
}
