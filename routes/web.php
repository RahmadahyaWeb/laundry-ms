<?php

use App\Livewire\Auth\Login;
use App\Livewire\LandingPage\AboutUs;
use App\Livewire\LandingPage\Index;
use App\Livewire\LandingPage\PaymentForm;
use App\Livewire\LandingPage\TrxForm;
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
Route::get('/about-us', AboutUs::class)->name('landing-page.about-us');
Route::get('/transaction-form', TrxForm::class)->name('landing-page.transaction-form');
Route::get('/payment-form/{invoice_number}', PaymentForm::class)->name('landing-page.payment-form');

Route::middleware(['guest'])->group(function () {
    Route::get('login', Login::class)->name('login');
});
