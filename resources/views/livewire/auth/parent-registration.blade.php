<div class="terminal-container" style="justify-content: center;">
    <div style="max-width: 400px; width: 100%;">
        <div class="terminal-line bright" style="font-size: 18px; margin-bottom: 24px;">
            PARENT REGISTRATION
        </div>

        <form wire:submit.prevent="register">
            <div class="terminal-form-field" style="margin-bottom: 16px;">
                <label style="width: 100px;">NAME:</label>
                <input type="text" wire:model="name" class="terminal-input" style="border-bottom: 1px solid var(--terminal-faint);">
            </div>
            @error('name')
                <div class="terminal-line warning" style="margin-bottom: 8px;">{{ $message }}</div>
            @enderror

            <div class="terminal-form-field" style="margin-bottom: 16px;">
                <label style="width: 100px;">EMAIL:</label>
                <input type="email" wire:model="email" class="terminal-input" style="border-bottom: 1px solid var(--terminal-faint);">
            </div>
            @error('email')
                <div class="terminal-line warning" style="margin-bottom: 8px;">{{ $message }}</div>
            @enderror

            <div class="terminal-form-field" style="margin-bottom: 16px;">
                <label style="width: 100px;">PASSWORD:</label>
                <input type="password" wire:model="password" class="terminal-input" style="border-bottom: 1px solid var(--terminal-faint);">
            </div>
            @error('password')
                <div class="terminal-line warning" style="margin-bottom: 8px;">{{ $message }}</div>
            @enderror

            <div class="terminal-form-field" style="margin-bottom: 16px;">
                <label style="width: 100px;">CONFIRM:</label>
                <input type="password" wire:model="password_confirmation" class="terminal-input" style="border-bottom: 1px solid var(--terminal-faint);">
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="side-button" style="writing-mode: horizontal-tb; padding: 8px 24px;">
                    REGISTER
                </button>
            </div>
        </form>

        <div class="terminal-line dim" style="margin-top: 24px;">
            Already have an account? <a href="{{ route('login') }}" style="color: var(--terminal-color);">Login</a>
        </div>
    </div>
</div>
