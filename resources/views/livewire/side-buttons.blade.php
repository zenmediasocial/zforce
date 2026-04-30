<div class="side-buttons">
    <div class="side-buttons-left">
        @foreach (array_filter($buttons, fn($b) => $b['position'] === 'left') as $button)
            <button wire:click="openPanel('{{ $button['id'] }}')"
                    class="side-button"
                    title="{{ $button['label'] }}">
                <span class="side-button-label">{{ $button['label'] }}</span>
            </button>
        @endforeach
    </div>

    <div class="side-buttons-right">
        @foreach (array_filter($buttons, fn($b) => $b['position'] === 'right') as $button)
            <button wire:click="openPanel('{{ $button['id'] }}')"
                    class="side-button"
                    title="{{ $button['label'] }}">
                <span class="side-button-label">{{ $button['label'] }}</span>
            </button>
        @endforeach
    </div>
</div>
