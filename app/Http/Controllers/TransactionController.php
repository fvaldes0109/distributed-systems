<?php

namespace App\Http\Controllers;

use App\Http\Services\TransactionService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{
    public function collectCash(Request $request)
    {
        if (Cache::has('cloudcomputing-live') && Cache::get('cloudcomputing-live') == false) {
            return response()->json(['message' => 'Circuit break: Cloud computing service is down'], 500);
        }

        $data = $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'description' => 'required|string',
            'userId' => 'required|string'
        ]);

        $transaction = TransactionService::createTransaction($data);

        if ($transaction == null) {
            return response()->json(['message' => 'Transaction service is taking too long to respond'], 500);
        }

        return response()->json($transaction, 201);
    }
}
