<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppWebhookProcessor; 
use App\Models\Conversation;
use App\Models\Message;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $webhookData = $request->all();

        $processor = new WhatsAppWebhookProcessor();
        $result = $processor->process($webhookData);

        if ($result['event_type'] == 'message_text' && isset($result['celular'])) {
            $celular = $result['celular'];
            $mensagem = $result['message'];

            $conversation = Conversation::firstOrCreate(
                ['phone' => $celular],
                ['last_message_at' => now(), 'status' => 'active']
            );

            $conversation->messages()->create([
                'sender' => 'user',
                'content' => $mensagem,
                'type' => 'text',
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}