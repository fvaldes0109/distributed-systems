<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::get('/cc-status', function () {
    return response()->json(['status' => 'Cloud computing service is ' . (Cache::get('cloudcomputing-live') ? 'up' : 'down')]);
});

Route::get('/transaction', [TransactionController::class, 'index']);
Route::post('/transaction', [TransactionController::class, 'collectCash']);
