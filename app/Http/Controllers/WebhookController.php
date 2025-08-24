<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppWebhookProcessor; 

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $webhookData = $request->all();

        $processor = new WhatsAppWebhookProcessor();
        $result = $processor->process($webhookData);

        return response()->json(['status' => 'ok']);
    }
}