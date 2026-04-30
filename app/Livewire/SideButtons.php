<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class SideButtons extends Component
{
    public array $buttons = [
        ['id' => 'multimedia', 'label' => 'MEDIA', 'position' => 'left'],
        ['id' => 'backstory', 'label' => 'LORE', 'position' => 'left'],
        ['id' => 'account', 'label' => 'USER', 'position' => 'right'],
        ['id' => 'stats', 'label' => 'STATS', 'position' => 'right'],
    ];

    public function openPanel(string $panel): void
    {
        $this->dispatch('open-panel', panel: $panel);
    }

    public function render()
    {
        return view('livewire.side-buttons');
    }
}
