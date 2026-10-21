<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Enterprise Billing & POS System
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Health Check
    Route::get('/health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()]));

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // POS & Cash Management
        Route::post('cash-sessions/open', [\App\Http\Controllers\CashSessionController::class, 'open']);
        Route::post('cash-sessions/{id}/close', [\App\Http\Controllers\CashSessionController::class, 'close']);
        Route::post('cash-sessions/{id}/movements', [\App\Http\Controllers\CashSessionController::class, 'addMovement']);

        // Inventory Kardex
        Route::get('kardex/{variantId}', [\App\Http\Controllers\StockMovementController::class, 'kardexByVariant']);

        // Invoicing API
        Route::apiResource('invoices', \App\Http\Controllers\InvoiceController::class);
        Route::post('invoices/{id}/issue', [\App\Http\Controllers\InvoiceController::class, 'issue']);
        Route::post('invoices/{id}/void', [\App\Http\Controllers\InvoiceController::class, 'void']);
        Route::get('invoices/{id}/pdf', [\App\Http\Controllers\InvoiceController::class, 'generatePdf']);
    });
});
