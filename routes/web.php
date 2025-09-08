
<?php

use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['guest'])->group(function (){
    Volt::route('/', 'pages.auth.login')
    ->name('login');
});

Volt::route('/users', 'users');


Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'pages.dashboard')
        ->name('dashboard');
    Volt::route('users', 'pages.users.users-index')
        ->name('users');
    Volt::route('jadwal', 'pages.jadwal.jadwal-index')
        ->name('jadwal');
    Volt::route('logbook', 'pages.logbook.logbook-index')
        ->name('logbook');
    Volt::route('laporan', 'pages.laporan')
        ->name('laporan');
    Route::view('laporan-jadwal-perbulan', 'pages.laporan-jadwal')
        ->name('laporan-jadwal');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});




// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

require __DIR__ . '/auth.php';
