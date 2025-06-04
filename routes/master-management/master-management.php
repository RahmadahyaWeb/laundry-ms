<?php

use App\Livewire\Menu\Campaign;
use App\Livewire\Menu\Customer;
use App\Livewire\Menu\Master\Service;
use App\Livewire\Menu\Master\ServiceCategory;

Route::prefix('master-management')->name('master-management.')->group(function () {
    Route::get('/service-categories', ServiceCategory::class)->name('service-categories')->middleware('permission:view service-category');
    Route::get('/services', Service::class)->name('services')->middleware('permission:view service');
    Route::get('/campaigns', Campaign::class)->name('campaigns')->middleware('permission:view campaign');
    Route::get('/customers', Customer::class)->name('customers')->middleware('permission:view customer');
});
