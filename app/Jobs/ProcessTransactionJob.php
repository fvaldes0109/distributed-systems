<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
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
                $response = Http::timeout(5)->post($url, $this->data);
                break;
            } catch (\Exception $e) {

            }

            // Adding exponential backoff with random jitter
            $random = mt_rand(0, $await);
            usleep($random * 1000);
            $await *= 2;
        }

        if ($response == null || !$response->successful()) {
            return;
        }

        $transactionId = $response->json()['data']['transactionId'];
        $status = $response->json()['data']['status'];

        Transaction::create([
            'transactionId' => $transactionId,
            'status' => $status,
            'amount' => $this->data['amount'],
            'currency' => $this->data['currency'],
            'description' => $this->data['description'],
            'userId' => $this->data['userId']
        ]);
    }
}
