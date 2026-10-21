<?php

use App\Http\Controllers\Web\InvoiceWebController;
use App\Http\Controllers\Web\TaskWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Enterprise Billing & POS System
| UI Inspired by Crater Invoice & Invoice Ninja Tasks
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('invoices.index'));

// Crater Invoice Web UI
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [InvoiceWebController::class, 'index'])->name('index');
    Route::get('/create', [InvoiceWebController::class, 'create'])->name('create');
    Route::post('/', [InvoiceWebController::class, 'store'])->name('store');
    Route::get('/{id}', [InvoiceWebController::class, 'show'])->name('show');
    Route::post('/{id}/issue', [InvoiceWebController::class, 'issue'])->name('issue');
    Route::post('/{id}/void', [InvoiceWebController::class, 'void'])->name('void');
    Route::get('/{id}/pdf', [InvoiceWebController::class, 'pdf'])->name('pdf');
});

// Invoice Ninja Task & Time Tracking Companion API & Web endpoints
Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskWebController::class, 'index'])->name('index');
    Route::post('/', [TaskWebController::class, 'store'])->name('store');
    Route::post('/{id}/start-timer', [TaskWebController::class, 'startTimer'])->name('start_timer');
    Route::post('/{id}/stop-timer', [TaskWebController::class, 'stopTimer'])->name('stop_timer');
    Route::post('/convert-to-invoice', [TaskWebController::class, 'convertToInvoiceUrl'])->name('convert_to_invoice');
});
