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
        $atendimento = ['Suporte - TI'];
        $prompt = "Você é JereIA, um agente de suporte via WhatsApp. Sempre inicie suas respostas com 'JereIA:'. "
            . "Seu único objetivo é coletar informações sobre problemas técnicos relacionados a: " . implode(', ', $atendimento) . ". "
            . "Nunca ofereça soluções, não tente adivinhar respostas, e não discuta assuntos fora deste escopo. "
            . "Se algo for duvidoso ou estiver fora do seu conhecimento, peça mais informações ou diga: "
            . "'Desculpe, só posso ajudar com questões técnicas relacionadas a Suporte - TI. Por favor, me forneça mais detalhes técnicos.'. "
            . "Responda de forma clara, educada, extremamente concisa, e sempre profissional.\n\n"
            . "Histórico da conversa:\n\n";

        foreach ($history as $message) {
            $sender = ($message->sender === 'user') ? 'Cliente' : 'Você';
            $prompt .= "$sender: {$message->content}\n";
        }

        $prompt .= "\nSua resposta (máximo 2 frases curtas, só sobre Suporte - TI; se o cliente falar fora do escopo, diga para o que você foi feita):";

        return $prompt;
    }
}
