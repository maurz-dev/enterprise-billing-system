<?php

use App\Http\Controllers\Web\InventoryWebController;
use App\Http\Controllers\Web\InvoiceWebController;
use App\Http\Controllers\Web\PosWebController;
use App\Http\Controllers\Web\TaskWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Enterprise Billing & POS System
| Unified Platform: Invoicing, Tasks, Inventory & POS
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('invoices.index'));

// Invoicing & Document Management
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [InvoiceWebController::class, 'index'])->name('index');
    Route::get('/create', [InvoiceWebController::class, 'create'])->name('create');
    Route::post('/', [InvoiceWebController::class, 'store'])->name('store');
    Route::get('/{id}', [InvoiceWebController::class, 'show'])->name('show');
    Route::post('/{id}/issue', [InvoiceWebController::class, 'issue'])->name('issue');
    Route::post('/{id}/void', [InvoiceWebController::class, 'void'])->name('void');
    Route::get('/{id}/pdf', [InvoiceWebController::class, 'pdf'])->name('pdf');
});

// Inventory, Stock & Kardex
Route::get('/inventory', [InventoryWebController::class, 'index'])->name('inventory.index');

// POS & Cash Register Terminal
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosWebController::class, 'index'])->name('index');
    Route::post('/open', [PosWebController::class, 'openSession'])->name('open');
    Route::post('/{id}/close', [PosWebController::class, 'closeSession'])->name('close');
});

// Task & Time Tracking Companion
Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskWebController::class, 'index'])->name('index');
    Route::post('/', [TaskWebController::class, 'store'])->name('store');
    Route::post('/{id}/start-timer', [TaskWebController::class, 'startTimer'])->name('start_timer');
    Route::post('/{id}/stop-timer', [TaskWebController::class, 'stopTimer'])->name('stop_timer');
    Route::post('/convert-to-invoice', [TaskWebController::class, 'convertToInvoiceUrl'])->name('convert_to_invoice');
});
