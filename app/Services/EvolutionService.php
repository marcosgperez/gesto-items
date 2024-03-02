<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
class EvolutionService {
    public function sendMessage ($instance, $message) {
        $url = env('EVOLUTION_URL') . '/message/sendText/' . $instance;
        $res = $this->makeRequest($url, $message);
        return $res->json();
    }

    private function makeRequest(string $path, array $data = [])
    {
        $res = Http::withHeaders(
            [
                'apiKey' => env('EVOLUTION_KEY')
            ]
        )->post($path, $data);
        return $res;
    }
    
}