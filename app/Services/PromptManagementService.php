<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PromptManagementService
{
    public function __construct(public Http $httpClient)
    {
    }

    /**
     * Generates embeddings
     */
    public function generateEmbedding(string $string): array
    {

        $result = Http::post('http://localhost:11434/api/embed', [
            "model" => "qwen3:1.7b",
            "input" => $string,
            "keep_alive" => "60m"
        ]);

        if (!$result->successful()) {
            $result->throw();
        }

        return $result->json()['embeddings'][0];
    }

    public function prompt(string $prompt): string
    {
        $result = Http::post("http://localhost:11434/api/generate", [
            "model" => "qwen3:1.7b",
            "prompt" => $prompt,
            "keep_alive" => "60m",
            "stream" => false,
            "format" => "json"
        ]);

        if (!$result->successful()) {
            $result->throw();
        }

        return $result->json()['response'];
    }
}
