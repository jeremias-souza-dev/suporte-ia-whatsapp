<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $token;
    protected $phoneId;
    protected $apiPhoneNumber;
    protected $apiVersion;

    public function __construct()
    {
        $this->token = config('whatsapp.token');
        $this->phoneId = config('whatsapp.phone_id');
        $this->apiPhoneNumber = config('whatsapp.api_phone_number'); 
        $this->apiVersion = config('whatsapp.api_version');
    }

    public function sendMessageText($recipientNumber, $text, $previewUrl = true)
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $recipientNumber,
            'type' => 'text',
            'text' => [
                'preview_url' => $previewUrl,
                'body' => $text,
            ],
        ];

        return $this->sendRequest($payload);
    }


    protected function sendRequest($data)
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneId}/messages";

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$this->token}",
            "Content-Type" => "application/json",
        ])->post($url, $data);

        return $response->json();
    }
}