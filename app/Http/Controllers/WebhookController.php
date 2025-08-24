<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppWebhookProcessor; 

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $webhookData = $request->all();

        file_put_contents(storage_path('app/webhook_data.json'), json_encode($webhookData, JSON_PRETTY_PRINT));

        $processor = new WhatsAppWebhookProcessor();
        $result = $processor->process($webhookData);

        return response()->json(['status' => 'ok']);
    }
}