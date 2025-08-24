<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;

class WhatsAppTestController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function sendTest()
    {
        $recipient = config('whatsapp.recipient_test', null);

        if (!$recipient) {
            return response()->json(['success' => false, 'message' => 'Número de telefone do destinatário não configurado no arquivo .env.'], 400);
        }

        $message = 'Olá! Essa é uma mensagem de teste enviada via WhatsApp API.';

        $response = $this->whatsapp->sendMessageText($recipient, $message);

        return response()->json([
            'success' => true,
            'response' => $response,
        ]);
    }
}
