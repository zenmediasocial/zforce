<div class="terminal-container" style="justify-content: center;">
    <div style="max-width: 400px; width: 100%;">
        <div class="terminal-line bright" style="font-size: 18px; margin-bottom: 24px;">
            LOGIN TO TERMINAL
        </div>

        <form wire:submit.prevent="login">
            <div class="terminal-form-field" style="margin-bottom: 16px;">
                <label style="width: 80px;">EMAIL:</label>
                <input type="email" wire:model="email" class="terminal-input" style="border-bottom: 1px solid var(--terminal-faint);">
            </div>
            @error('email')
                <div class="terminal-line warning" style="margin-bottom: 8px;">{{ $message }}</div>
            @enderror

            <div class="terminal-form-field" style="margin-bottom: 16px;">
                <label style="width: 80px;">PASSWORD:</label>
                <input type="password" wire:model="password" class="terminal-input" style="border-bottom: 1px solid var(--terminal-faint);">
            </div>
            @error('password')
                <div class="terminal-line warning" style="margin-bottom: 8px;">{{ $message }}</div>
            @enderror

            <div style="margin-top: 24px;">
                <button type="submit" class="side-button" style="writing-mode: horizontal-tb; padding: 8px 24px;">
                    LOGIN
                </button>
            </div>
        </form>

        <div class="terminal-line dim" style="margin-top: 24px;">
            No account? <a href="{{ route('register') }}" style="color: var(--terminal-color);">Register as parent</a>
        </div>
    </div>
</div>
