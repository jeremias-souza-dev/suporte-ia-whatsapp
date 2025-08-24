<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class GeminiTestController extends Controller
{
    public function sendTest()
    {
        $gemini = new GeminiService();

        $prompt = "Explique de forma simples como um sistema de suporte via WhatsApp funciona.";
        $resposta = $gemini->generateText($prompt);

        return response()->json([
            'prompt' => $prompt,
            'resposta' => $resposta,
        ]);
    }
}
