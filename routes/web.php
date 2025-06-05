<?php

use App\Livewire\Auth\Login;
use App\Livewire\LandingPage\Index;
use App\Livewire\Menu\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('permission:view dashboard');

    require __DIR__ . '/user-management/user-management.php';
    require __DIR__ . '/master-management/master-management.php';
    require __DIR__ . '/trx-management/trx-management.php';

    Route::get('/logout', function () {
        Auth::logout();

        return redirect('/');
    });
});

Route::get('/', Index::class)->name('landing-page');

Route::middleware(['guest'])->group(function () {
    Route::get('login', Login::class)->name('login');
});
