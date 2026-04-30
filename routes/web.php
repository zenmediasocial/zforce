<?php

use App\Http\Controllers\PageController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ParentRegistration;
use Illuminate\Support\Facades\Route;

// Public pages (zforce.army platform)
Route::get('/', [PageController::class, 'landing'])->name('landing');
Route::get('/chronicles', [PageController::class, 'chronicles'])->name('chronicles');
Route::get('/chronicles/{slug}', [PageController::class, 'chronicle'])->name('chronicle.show');
Route::get('/missions', [PageController::class, 'missions'])->name('missions');
Route::get('/missions/{slug}', [PageController::class, 'mission'])->name('mission.show');
Route::get('/lore', [PageController::class, 'lore'])->name('lore');
Route::get('/about', [PageController::class, 'about'])->name('about');

// Terminal (requires auth)
Route::get('/terminal', function () {
    return view('app');
})->name('terminal');

Route::get('/login', Login::class)->name('login');
Route::get('/register', ParentRegistration::class)->name('register');
