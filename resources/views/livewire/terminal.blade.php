<div x-data="terminal()"
     x-init="init(); $nextTick(() => { if ($refs.input) $refs.input.focus(); })"
     @type-lines.window="typeLines($event.detail.lines)"
     @lines-typed.window="$wire.addTypedLines($event.detail.lines)"
     @boot-complete.window="$wire.bootComplete()"
     class="crt"
     @click="$refs.input && $refs.input.focus()">

    <div class="screen-glow"></div>

    <div class="terminal-container">
        {{-- Output area: boot sequence OR terminal content --}}
        <div class="terminal-output" x-ref="output">
            @if (!$booted)
                <div wire:ignore.self>
                    <livewire:boot-sequence :sequence="$bootSequence" />
                </div>
            @else
                @foreach ($lines as $line)
                    <div class="terminal-line {{ $line['class'] ?? '' }}">
                        {!! nl2br(e($line['text'])) !!}
                    </div>
                @endforeach

                <template x-for="line in typingLines" :key="line.id">
                    <div :class="'terminal-line ' + line.class" x-text="line.text"></div>
                </template>
            @endif
        </div>

        {{-- Input line: ALWAYS visible --}}
        <div class="terminal-input-line" :class="{ 'input-disabled': !@js($booted) || isTyping }">
            <span class="terminal-prompt">C:\&gt;</span>
            <input type="text"
                   x-ref="input"
                   wire:model.defer="input"
                   wire:keydown.enter="processCommand"
                   wire:keydown.arrow-up.prevent="historyUp"
                   wire:keydown.arrow-down.prevent="historyDown"
                   class="terminal-input"
                   autocomplete="off"
                   spellcheck="false"
                   placeholder="Type a command..."
                   :disabled="!@js($booted) || isTyping"
                   @focus="$el.parentElement.classList.add('input-active')"
                   @blur="$el.parentElement.classList.remove('input-active')"
                   autofocus>
            <span class="cursor-blink" :class="{ 'cursor-paused': isTyping }"></span>
        </div>
    </div>

    @if ($booted)
        <div class="terminal-footer">
            <span class="easter-egg">Knowledge is power. Power corrupts. Study hard. Be evil.</span>
            <span>TERMINAL LEARNING SYSTEM v1.0</span>
        </div>
    @endif

    <livewire:side-buttons />

    <livewire:off-canvas-panel name="multimedia" />
    <livewire:off-canvas-panel name="backstory" />
    <livewire:off-canvas-panel name="account" />
    <livewire:off-canvas-panel name="stats" />
</div>
