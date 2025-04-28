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

        $result = $this->httpClient->post('http://localhost:11434/api/embed', [
            "model" => "tinyllama:1.1b",
            "input" => $string,
            "keep_alive" => "60m"
        ]);

        if (!$result->successful()) {
            $result->throw();
        }

        return $result->json()['embeddings'][0];
    }
}
