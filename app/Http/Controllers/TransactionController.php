<?php

namespace App\Http\Controllers;

use App\Http\Services\TransactionService;
use App\Jobs\ProcessTransactionJob;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::all();
    }

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

        ProcessTransactionJob::dispatch($data);

        return response()->json(['message' => 'Transaction enqueued successfully'], 200);
    }
}
