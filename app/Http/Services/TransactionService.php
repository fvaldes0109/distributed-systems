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

        $url = "$host:$port/v1/wallet/transaction";

        $response = null;
        $attempt = 0;
        $await = 100;

        // Retrying 5 times with exponential backoff
        while ($attempt++ < 5) {
            try {
                $response = Http::timeout(5)->post($url, $data);
                break;
            } catch (\Exception $e) {

            }

            // Adding exponential backoff with random jitter
            $random = mt_rand(0, $await);
            usleep($random * 1000);
            $await *= 2;
        }

        if ($response == null || !$response->successful()) {
            return null;
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
