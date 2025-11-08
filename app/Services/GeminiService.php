<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private ?string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com';
    private string $apiVersion = 'v1';
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        // Use model name without -latest suffix for v1 API compatibility
        $this->model = env('GEMINI_MODEL', 'gemini-2.5-flash');
    }

    private function getApiUrl(): string
    {
        return "{$this->baseUrl}/{$this->apiVersion}/models/{$this->model}:generateContent";
    }

    public function generateTweets(string $blogPostContent, string $blogPostTitle = ''): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('GEMINI_API_KEY is not set in environment variables');
        }

        $prompt = $this->buildPrompt($blogPostContent, $blogPostTitle);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->getApiUrl() . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to generate tweets: ' . $response->body());
            }

            $data = $response->json();
            
            if (!isset($data['candidates']) || empty($data['candidates'])) {
                throw new \Exception('No candidates returned from Gemini API. Response: ' . json_encode($data));
            }

            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (empty($text)) {
                throw new \Exception('Empty response from Gemini API. Response: ' . json_encode($data));
            }

            $tweets = $this->parseTweets($text);
            
            if (empty($tweets)) {
                throw new \Exception('Failed to parse tweets from Gemini response. Response text: ' . substr($text, 0, 500));
            }

            return $tweets;
        } catch (\Exception $e) {
            Log::error('Gemini service error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function buildPrompt(string $blogPostContent, string $blogPostTitle): string
    {
        $titlePart = $blogPostTitle ? "Title: {$blogPostTitle}\n\n" : '';
        
        return "You are a social media content creator. Based on the following blog post, generate exactly 10 tweets that summarize key points, insights, or quotes from the content.

IMPORTANT REQUIREMENTS:
- Each tweet must be 280 characters or less
- Do NOT include any hashtags (#)
- Do NOT include any mentions (@)
- Each tweet should be on its own line, numbered 1-10
- Make the tweets engaging and shareable
- Focus on different aspects of the blog post for variety
- Write in a conversational, engaging tone

{$titlePart}Blog Post Content:
{$blogPostContent}

Generate 10 tweets, one per line, numbered 1-10:";
    }

    private function parseTweets(string $text): array
    {
        $lines = explode("\n", $text);
        $tweets = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Remove numbering (1., 2., etc.) or bullet points
            $line = preg_replace('/^\d+[\.\)]\s*/', '', $line);
            $line = preg_replace('/^[-•*]\s*/', '', $line);
            $line = trim($line);

            // Remove hashtags and mentions if any
            $line = preg_replace('/#\w+/', '', $line);
            $line = preg_replace('/@\w+/', '', $line);
            $line = trim($line);

            if (!empty($line) && mb_strlen($line) <= 280) {
                $tweets[] = $line;
            }
        }

        // Ensure we have exactly 10 tweets
        if (count($tweets) > 10) {
            $tweets = array_slice($tweets, 0, 10);
        }

        // If we have fewer than 10, pad with empty strings or repeat logic
        while (count($tweets) < 10) {
            $tweets[] = '';
        }

        return array_filter($tweets, fn($tweet) => !empty($tweet));
    }
}

