<?php

namespace App\Http\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class TransactionService
{
    public static function createTransaction(array $data)
    {
        $host = config('services.cloudcomputing.host');
        $port = config('services.cloudcomputing.port');

        $response = Http::post("http://$host:$port/v1/wallet/transaction", $data);

        if ($response->status() !== 200) {
            return response()->json(['message' => 'Failed to create transaction'], 500);
        }

        $transactionId = $response->json()['data']['transactionId'];
        $status = $response->json()['data']['status'];

        return Transaction::create([
            'transactionId' => $transactionId,
            'status' => $status,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'description' => $data['description'],
            'userId' => $data['userId']
        ]);
    }
}
