<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $baseUrl = 'https://api.fonnte.com/send';
    protected $token;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
    }

    public function sendMessage(string $target, string $message, string $countryCode = '62'): array
    {
        try {
            // Log sebelum request
            Log::info('Mengirim pesan WhatsApp', [
                'target' => $target,
                'message' => $message,
                'countryCode' => $countryCode,
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type'  => 'application/json',
            ])->asForm()->post($this->baseUrl, [
                'target' => $target,
                'message' => $message,
                'countryCode' => $countryCode,
            ]);

            // Log response dari Fonnte
            Log::info('Respons dari Fonnte', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            // Log error
            Log::error('Gagal mengirim pesan WhatsApp', [
                'target' => $target,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
