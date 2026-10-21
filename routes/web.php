<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'system' => 'Enterprise Invoicing, Inventory & POS API',
        'status' => 'active',
        'documentation' => '/docs'
    ]);
});
