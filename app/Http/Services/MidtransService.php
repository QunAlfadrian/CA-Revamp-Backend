<?php
namespace App\Http\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MidtransService {
    /**
     * Get Transaction Snap Token.
     *
     * @return array
     */
    public function snap(array $payload) {
        $serverKey = config('midtrans.server_key');
        $authString = base64_encode("{$serverKey}:");

        $client = new Client();
        $response = $client->post(config('midtrans.snap_api_endpoint'), [
            'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => "Basic {$authString}",
                ],
            'body' => json_encode($payload)
        ]);

        try {
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error([
                "message" => "Midtrans Snap Request Error",
                "data" => $data
            ]);
        }
        return $data;
    }
}
