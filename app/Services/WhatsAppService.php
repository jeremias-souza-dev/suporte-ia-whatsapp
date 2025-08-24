<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $token;
    protected $phoneId;

    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->phoneId = env('WHATSAPP_PHONE_ID');
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
        $url = "https://graph.facebook.com/v18.0/{$this->phoneId}/messages";
        return Http::withToken($this->token)
            ->post($url, $data)
            ->json();
    }
}