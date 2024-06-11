<?php

namespace App\Callables;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CCHealthCheck
{
    public function __invoke()
    {
        $host = config('services.cloudcomputing.host');
        $port = config('services.cloudcomputing.port');

        $url = "$host:$port";

        try {
            $response = Http::timeout(5)->get($url);
            Cache::put('cloudcomputing-live', true);
        } catch (\Exception $e) {
            Cache::set('cloudcomputing-live', false);
        }
    }
}
