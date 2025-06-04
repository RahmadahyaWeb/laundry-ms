<?php

use App\Livewire\Menu\Transaction;
use App\Livewire\Menu\TransactionForm;

Route::prefix('transaction-management')->name('transaction-management.')->group(function () {
    Route::get('/transactions', Transaction::class)->name('transactions')->middleware('permission:view trx');
    Route::get('/form', TransactionForm::class)->name('form')->middleware('permission:view trx-form');
});
