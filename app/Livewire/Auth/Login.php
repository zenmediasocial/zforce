<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($validated, $this->remember)) {
            $this->redirectRoute('terminal');
        } else {
            $this->addError('email', 'Invalid credentials.');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layout', ['title' => 'Login - Terminal']);
    }
}
