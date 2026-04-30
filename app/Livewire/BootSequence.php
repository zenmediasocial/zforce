<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class BootSequence extends Component
{
    public array $sequence = [];

    public function mount(array $sequence = []): void
    {
        $this->sequence = $sequence;
    }

    public function complete(): void
    {
        $this->dispatch('boot-complete');
    }

    public function render()
    {
        return view('livewire.boot-sequence');
    }
}
