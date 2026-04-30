<?php

declare(strict_types=1);

namespace App\Services\Vortex;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterGateway
{
    private string $baseUrl;
    private ?string $apiKey;
    private array $defaultParams;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('openrouter.base_url'), '/');
        $this->apiKey = config('openrouter.api_key');
        $this->defaultParams = config('openrouter.defaults', []);
    }

    /**
     * Send a chat completion request through the vortex.
     *
     * @param array $messages OpenAI-format messages
     * @param string $tier Model tier (economy, standard, premium, frontier)
     * @param array $options Override defaults (temperature, max_tokens, etc.)
     */
    public function chat(array $messages, string $tier = 'standard', array $options = []): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenRouter API key not configured. Set OPENROUTER_API_KEY in .env');
        }

        $model = $this->resolveModel($tier);

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? $this->defaultParams['temperature'] ?? 0.85,
            'max_tokens' => $options['max_tokens'] ?? $this->defaultParams['max_tokens'] ?? 1024,
        ];

        if (isset($options['response_format'])) {
            $payload['response_format'] = $options['response_format'];
        }

        $startTime = microtime(true);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url', 'https://zforce.local'),
                'X-Title' => 'Zforce Temporal Interface',
            ])
                ->timeout($options['timeout'] ?? $this->defaultParams['timeout'] ?? 30)
                ->post($this->baseUrl . '/chat/completions', $payload);

            $latency = round((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                Log::warning('Vortex transmission failed', [
                    'model' => $model,
                    'tier' => $tier,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \RuntimeException('OpenRouter error: ' . $response->body());
            }

            $data = $response->json();
            $usage = $data['usage'] ?? [];

            return [
                'content' => $data['choices'][0]['message']['content'] ?? '',
                'model' => $model,
                'tier' => $tier,
                'latency_ms' => $latency,
                'prompt_tokens' => $usage['prompt_tokens'] ?? 0,
                'completion_tokens' => $usage['completion_tokens'] ?? 0,
                'total_tokens' => $usage['total_tokens'] ?? 0,
                'cost_estimate' => $this->estimateCost($usage['total_tokens'] ?? 0, $tier),
                'raw' => $data,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Vortex connection lost', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Vortex connection lost. The tunnel has collapsed.');
        }
    }

    /**
     * Classify intent using a cheap model call.
     */
    public function classifyIntent(string $userMessage, array $context = []): string
    {
        if (empty($this->apiKey)) {
            return 'unknown';
        }

        $systemPrompt = "You are a fast intent classifier for the Zforce temporal interface. "
            . "Classify the user's message into exactly one category. "
            . "Respond with ONLY the category name, no explanation.\n\n"
            . "Categories: greeting, question, mission_request, status_check, "
            . "choice_selection, report_submission, emotional_response, unknown\n\n"
            . "Context: " . json_encode($context);

        try {
            $result = $this->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ], 'economy', ['max_tokens' => 50, 'temperature' => 0.1]);

            $intent = strtolower(trim($result['content']));

            // Normalize
            $validIntents = ['greeting', 'question', 'mission_request', 'status_check', 'choice_selection', 'report_submission', 'emotional_response', 'unknown'];

            return in_array($intent, $validIntents, true) ? $intent : 'unknown';
        } catch (\RuntimeException) {
            return 'unknown';
        }
    }

    /**
     * Resolve a tier to an actual model name.
     */
    private function resolveModel(string $tier): string
    {
        $models = config('openrouter.models', []);

        return $models[$tier] ?? $models['standard'] ?? 'anthropic/claude-3.5-haiku';
    }

    /**
     * Rough cost estimation based on token count.
     */
    private function estimateCost(int $tokens, string $tier): float
    {
        // Rough per-1M token pricing (input + output averaged)
        $prices = [
            'economy' => 0.50,
            'standard' => 3.00,
            'premium' => 15.00,
            'frontier' => 25.00,
        ];

        $pricePerM = $prices[$tier] ?? 3.00;

        return round(($tokens / 1000000) * $pricePerM, 6);
    }
}
