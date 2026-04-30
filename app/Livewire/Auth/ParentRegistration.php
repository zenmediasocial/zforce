<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ParentRegistration extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_child' => false,
        ]);

        // Create personal team
        $team = Team::create([
            'name' => $user->name . "'s Family",
            'display_name' => $user->name . "'s Family",
            'personal_team' => true,
        ]);

        $user->teams()->attach($team);
        $user->current_team_id = $team->id;
        $user->save();

        $user->assignRole('team-admin');

        Auth::login($user);
        $this->redirectRoute('terminal');
    }

    public function render()
    {
        return view('livewire.auth.parent-registration')
            ->layout('components.layout', ['title' => 'Register - Terminal']);
    }
}
