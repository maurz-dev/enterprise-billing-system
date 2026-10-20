<?php

use IlluminateSupportFacadesRoute;

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
        // Tenancy & Core
        Route::apiResource('companies', 'App\Http\Controllers\CompanyController');
        Route::apiResource('branches', 'App\Http\Controllers\BranchController');
        Route::apiResource('warehouses', 'App\Http\Controllers\WarehouseController');

        // Contacts (Customers & Suppliers)
        Route::apiResource('contacts', 'App\Http\Controllers\ContactController');

        // Catalog & Inventory
        Route::apiResource('categories', 'App\Http\Controllers\CategoryController');
        Route::apiResource('products', 'App\Http\Controllers\ProductController');
        Route::get('stocks', 'App\Http\Controllers\StockController@index');
        Route::get('kardex/{variantId}', 'App\Http\Controllers\StockMovementController@kardexByVariant');

        // POS & Cash Management
        Route::apiResource('cash-registers', 'App\Http\Controllers\CashRegisterController');
        Route::post('cash-sessions/open', 'App\Http\Controllers\CashSessionController@open');
        Route::post('cash-sessions/{id}/close', 'App\Http\Controllers\CashSessionController@close');
        Route::post('cash-sessions/{id}/movements', 'App\Http\Controllers\CashSessionController@addMovement');

        // Sales & Invoicing
        Route::apiResource('quotes', 'App\Http\Controllers\QuoteController');
        Route::post('quotes/{id}/convert-to-invoice', 'App\Http\Controllers\QuoteController@convertToInvoice');

        Route::apiResource('invoices', 'App\Http\Controllers\InvoiceController');
        Route::post('invoices/{id}/issue', 'App\Http\Controllers\InvoiceController@issue');
        Route::post('invoices/{id}/void', 'App\Http\Controllers\InvoiceController@void');
        Route::post('invoices/{id}/payments', 'App\Http\Controllers\InvoicePaymentController@store');
        Route::get('invoices/{id}/pdf', 'App\Http\Controllers\InvoiceController@generatePdf');

        // Purchases
        Route::apiResource('purchases', 'App\Http\Controllers\PurchaseController');
        Route::post('purchases/{id}/receive', 'App\Http\Controllers\PurchaseController@receive');
    });
});
