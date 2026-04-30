<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\ParentRegistration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
})->name('terminal');

Route::get('/login', Login::class)->name('login');
Route::get('/register', ParentRegistration::class)->name('register');
