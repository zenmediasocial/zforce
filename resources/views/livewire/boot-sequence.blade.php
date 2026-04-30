<div x-data="{ currentLine: 0 }"
     x-init="
        const interval = setInterval(() => {
            currentLine++;
            if (currentLine >= {{ count($sequence) }}) {
                clearInterval(interval);
                $dispatch('boot-complete');
            }
        }, 300);
     "
     class="terminal-container">
    @foreach ($sequence as $index => $line)
        <div x-show="currentLine >= {{ $index }}"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="terminal-line {{ $line['class'] ?? '' }}">
            {{ $line['text'] }}
        </div>
    @endforeach
    <div x-show="currentLine >= {{ count($sequence) }}" class="terminal-line dim">System ready.</div>
</div>
