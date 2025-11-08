<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tweet Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for tweet generation and validation.
    |
    */

    'tweet' => [
        'max_character_count' => env('TWEET_MAX_CHARACTERS', 280),
        'count' => env('TWEET_COUNT', 10),
        'statuses' => ['draft', 'posted', 'discarded'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for pagination across the application.
    |
    */

    'pagination' => [
        'posts_per_page' => env('POSTS_PER_PAGE', 12),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini API Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for Gemini API tweet generation.
    |
    */

    'gemini' => [
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'top_k' => env('GEMINI_TOP_K', 40),
        'top_p' => env('GEMINI_TOP_P', 0.95),
        'max_output_tokens' => env('GEMINI_MAX_OUTPUT_TOKENS', 2048),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    |
    | Application-specific settings for UI and branding.
    |
    */

    'app' => [
        'name' => env('APP_NAME', 'Threadfarm'),
        'title' => env('APP_TITLE', 'Threadfarm - Blog Post to Tweets'),
    ],

];

