<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::post('/transaction', [TransactionController::class, 'collectCash']);
