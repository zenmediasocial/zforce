<?php

declare(strict_types=1);

namespace App\Services\Vortex;

class TransmissionResult
{
    public function __construct(
        public string $role,
        public string $content,
        public ?array $choices = null,
        public string $source = 'archive',
        public ?string $model = null,
        public string $vortexState = VortexState::DOWN,
        public ?float $cost = null,
        public ?int $latencyMs = null,
        public ?int $countdownDuration = null,
        public ?string $archiveSlug = null,
    ) {}

    public function withVortexState(string $state): self
    {
        return new self(
            role: $this->role,
            content: $this->content,
            choices: $this->choices,
            source: $this->source,
            model: $this->model,
            vortexState: $state,
            cost: $this->cost,
            latencyMs: $this->latencyMs,
            countdownDuration: $this->countdownDuration,
            archiveSlug: $this->archiveSlug,
        );
    }

    public function hasCountdown(): bool
    {
        return $this->countdownDuration !== null && $this->countdownDuration > 0;
    }

    public function isRealtime(): bool
    {
        return $this->source === 'realtime';
    }

    public function isArchive(): bool
    {
        return $this->source === 'archive';
    }
}
