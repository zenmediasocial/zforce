<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | OpenRouter API Configuration
    |--------------------------------------------------------------------------
    |
    | The Vortex connects to OpenRouter as its primary temporal gateway.
    | A single API key routes to multiple models based on task classification.
    |
    */

    'api_key' => env('OPENROUTER_API_KEY'),
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Model Routing (Adaptive by Task Class)
    |--------------------------------------------------------------------------
    |
    | Each task class maps to a model tier. Cheaper models handle routine
    | archive work. Frontier models handle critical narrative moments.
    |
    */

    'models' => [
        // Tier 1: Cheap & fast — classification, simple responses, archive retrieval
        'economy' => env('OPENROUTER_MODEL_ECONOMY', 'qwen/qwen-2.5-7b-instruct'),

        // Tier 2: Balanced — mission generation, lore expansion, blog drafts
        'standard' => env('OPENROUTER_MODEL_STANDARD', 'anthropic/claude-3.5-haiku'),

        // Tier 3: Frontier — first contact, emotional moments, complex adaptation
        'premium' => env('OPENROUTER_MODEL_PREMIUM', 'anthropic/claude-3.5-sonnet'),

        // Tier 4: Maximum capability — only when absolutely necessary
        'frontier' => env('OPENROUTER_MODEL_FRONTIER', 'anthropic/claude-3.7-sonnet'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Task Classification Rules
    |--------------------------------------------------------------------------
    |
    | Each intent maps to a tier. The TaskClassifier uses these plus
    | context heuristics (phase, trust level, mission criticality).
    |
    */

    'task_classes' => [
        // Archive operations — no LLM needed
        'archive_retrieval' => ['tier' => 'none', 'cost' => 0],
        'archive_render' => ['tier' => 'none', 'cost' => 0],

        // Economy tier — fast classification and simple generation
        'intent_classification' => ['tier' => 'economy', 'cost' => 0.001],
        'simple_response' => ['tier' => 'economy', 'cost' => 0.001],
        'mission_template' => ['tier' => 'economy', 'cost' => 0.002],

        // Standard tier — creative generation with quality
        'mission_generation' => ['tier' => 'standard', 'cost' => 0.005],
        'lore_expansion' => ['tier' => 'standard', 'cost' => 0.005],
        'blog_draft' => ['tier' => 'standard', 'cost' => 0.008],
        'report_assessment' => ['tier' => 'standard', 'cost' => 0.005],

        // Premium tier — emotionally resonant, adaptive narrative
        'first_contact' => ['tier' => 'premium', 'cost' => 0.02],
        'adaptive_narrative' => ['tier' => 'premium', 'cost' => 0.015],
        'critical_moment' => ['tier' => 'premium', 'cost' => 0.02],
        'trust_dialogue' => ['tier' => 'premium', 'cost' => 0.015],

        // Frontier tier — reserved for maximum impact moments
        'arc_climax' => ['tier' => 'frontier', 'cost' => 0.05],
        'faction_assignment' => ['tier' => 'frontier', 'cost' => 0.04],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vortex Cost Control
    |--------------------------------------------------------------------------
    |
    | Hard limits to prevent runaway costs. The vortex can only sustain
    | so many transmissions per session.
    |
    */

    'limits' => [
        'max_cost_per_session' => env('OPENROUTER_MAX_COST_PER_SESSION', 0.50),
        'max_premium_calls_per_session' => env('OPENROUTER_MAX_PREMIUM_CALLS', 5),
        'max_frontier_calls_per_session' => env('OPENROUTER_MAX_FRONTIER_CALLS', 1),
        'fallback_to_archive_on_limit' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'temperature' => 0.85,
        'max_tokens' => 1024,
        'timeout' => 30,
    ],
];
