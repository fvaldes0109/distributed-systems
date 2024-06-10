<?php

namespace App\Http\Controllers;

use App\Http\Services\TransactionService;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function collectCash(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'description' => 'required|string',
            'userId' => 'required|string'
        ]);

        $transaction = TransactionService::createTransaction($data);

        return response()->json($transaction, 201);
    }
}
