<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiKey;
    private string $username;
    private string $senderId;

    public function __construct()
    {
        $this->apiKey   = config('services.africastalking.api_key');
        $this->username = config('services.africastalking.username');
        $this->senderId = config('services.africastalking.sender_id', 'NyumbaFind');
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                'username' => $this->username,
                'to'       => $phone,
                'message'  => $message,
                'from'     => $this->senderId,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent to {$phone}");
                return true;
            }

            Log::error("SMS failed to {$phone}: " . $response->body());
            return false;

        } catch (\Throwable $e) {
            Log::error("SMS exception: " . $e->getMessage());
            return false;
        }
    }
}
