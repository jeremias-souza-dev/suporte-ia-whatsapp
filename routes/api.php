<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('webhook/whatsapp')->group(function () {
    Route::post('/', [WebhookController::class, 'handle']);
    Route::get('/', function () {
        $webhookData = [
            'query' => $_GET,
            'headers' => getallheaders(),
        ];
        file_put_contents(storage_path('app/webhook_data.json'), json_encode($webhookData, JSON_PRETTY_PRINT));
        return response($_REQUEST['hub_challenge'] ?? '');
    });
});

