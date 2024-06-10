<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/transaction', [TransactionController::class, 'collectCash']);
