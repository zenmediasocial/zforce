<div x-data="{ open: @entangle('isOpen') }"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-x-full opacity-0"
     x-transition:enter-end="translate-x-0 opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-x-0 opacity-100"
     x-transition:leave-end="translate-x-full opacity-0"
     class="off-canvas-panel"
     :class="'panel-' + '{{ $name }}'"
     style="display: none;">

    <div class="panel-header">
        <h2 class="panel-title">{{ $title }}</h2>
        <button wire:click="close" class="panel-close">[X]</button>
    </div>

    <div class="panel-content">
        @foreach ($content as $line)
            <div class="panel-line">{{ $line }}</div>
        @endforeach
    </div>
</div>
