<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\GeminiService;

class AgenteSuporte
{
    protected GeminiService $geminiService;
    protected WhatsAppService $whatsAppService;

    public function __construct(GeminiService $geminiService, WhatsAppService $whatsAppService)
    {
        $this->geminiService = $geminiService;
        $this->whatsAppService = $whatsAppService;
    }

    public function replyToMessage(Conversation $conversation): void
    {
        $history = $conversation->messages()->orderBy('created_at')->get();

        $prompt = $this->buildPromptFromHistory($history);

        $aiResponse = $this->geminiService->generateText($prompt);

        if ($aiResponse) {

            $conversation->messages()->create([
                'sender' => 'IA',
                'content' => $aiResponse,
                'type' => 'text',
            ]);

            $this->whatsAppService->sendMessageText($conversation->phone, $aiResponse);
        }
    }

    private function buildPromptFromHistory($history): string
    {
        $prompt = "Você é um agente de suporte via WhatsApp. Responda de forma clara, educada e concisa, sem se identificar como IA. Mantenha o tom profissional e direto. Leve em consideração o histórico da conversa para dar a melhor resposta.\n\nHistórico da conversa:\n\n";

        foreach ($history as $message) {
            $sender = ($message->sender === 'user') ? 'Cliente' : 'Você';
            $prompt .= "$sender: {$message->content}\n";
        }

        $prompt .= "\nSua resposta:";

        return $prompt;
    }
}