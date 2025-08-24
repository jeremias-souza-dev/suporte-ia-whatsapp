<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $modelId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->modelId = 'gemini-2.0-flash';
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->modelId}:generateContent";
    }

    public function generate(string $prompt, ?array $generationConfig = null): ?array
    {
        $defaultConfig = ['responseMimeType' => 'application/json'];
        $finalGenerationConfig = array_merge($defaultConfig, $generationConfig ?? []);

        try {
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => $finalGenerationConfig,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Gemini API retornou erro', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Erro ao chamar Gemini API', ['message' => $e->getMessage()]);
        }

        return null;
    }

    public function generateText(string $prompt, ?array $generationConfig = null): ?string
    {
        $generationConfig = array_merge($generationConfig ?? [], ['responseMimeType' => 'text/plain']);
        $body = $this->generate($prompt, $generationConfig);
        if (!$body) return null;

        return self::extractGeneratedText($body);
    }

    public function generateHtml(string $prompt, ?array $generationConfig = null): ?string
    {
        $generationConfig = array_merge($generationConfig ?? [], ['responseMimeType' => 'text/html']);
        return $this->generateText($prompt, $generationConfig);
    }

    public function generateMarkdown(string $prompt, ?array $generationConfig = null): ?string
    {
        $generationConfig = array_merge($generationConfig ?? [], ['responseMimeType' => 'text/markdown']);
        return $this->generateText($prompt, $generationConfig);
    }

    public function generateJson(string $prompt, ?array $generationConfig = null): ?array
    {
        $generationConfig = array_merge($generationConfig ?? [], ['responseMimeType' => 'application/json']);
        $body = $this->generate($prompt, $generationConfig);
        if (!$body) return null;

        return self::extractAndDecodeJson($body);
    }

    public static function extractGeneratedText(array $body): string
    {
        $texts = [];
        foreach ($body['candidates'] ?? [] as $candidate) {
            foreach ($candidate['content']['parts'] ?? [] as $part) {
                if (isset($part['text'])) {
                    $texts[] = $part['text'];
                }
            }
        }
        return implode("\n\n", $texts);
    }

    public static function extractAndDecodeJson(array $body): ?array
    {
        $text = self::extractGeneratedText($body);

        $decodedJson = json_decode($text, true);

        if ($decodedJson === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Falha ao decodificar JSON da resposta do Gemini: " . json_last_error_msg());
        }

        return $decodedJson;
    }
}
