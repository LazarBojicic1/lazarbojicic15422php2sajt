<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'tmdb' => [
        'api_key' => env('TMDB_API_KEY'),
        'base_url' => 'https://api.themoviedb.org/3',
        'image_base_url' => 'https://image.tmdb.org/t/p/',
    ],

    'streaming' => [
        'providers' => [
            [
                'key' => 'vidsrc',
                'label' => 'VidSrc',
                'id' => 'tmdb',
                'priority' => ['movie' => 10, 'tv' => 10],
                'movie' => 'https://vidsrc.mov/embed/movie/{id}',
                'tv' => 'https://vidsrc.mov/embed/tv/{id}/{season}/{episode}',
            ],
            [
                'key' => 'vidplus',
                'label' => 'VidPlus',
                'id' => 'tmdb',
                'priority' => ['movie' => 20, 'tv' => 20],
                'movie' => 'https://player.vidplus.to/embed/movie/{id}',
                'tv' => 'https://player.vidplus.to/embed/tv/{id}/{season}/{episode}',
            ],
            [
                'key' => 'vidsrc-alt',
                'label' => 'VidSrc Alt',
                'id' => 'tmdb',
                'priority' => ['movie' => 30, 'tv' => 30],
                'movie' => 'https://vidsrc-embed.ru/embed/movie/{id}',
                'tv' => 'https://vidsrc-embed.ru/embed/tv/{id}/{season}-{episode}',
            ],
            [
                'key' => 'vidsrc-imdb',
                'label' => 'VidSrc IMDb',
                'id' => 'imdb',
                'priority' => ['movie' => 40, 'tv' => 40],
                'movie' => 'https://vidsrc.mov/embed/movie/{id}',
                'tv' => 'https://vidsrc.mov/embed/tv/{id}/{season}/{episode}',
            ],
            [
                'key' => 'vidsrc-alt-imdb',
                'label' => 'VidSrc Alt IMDb',
                'id' => 'imdb',
                'priority' => ['movie' => 50, 'tv' => 50],
                'movie' => 'https://vidsrc-embed.ru/embed/movie/{id}',
                'tv' => 'https://vidsrc-embed.ru/embed/tv/{id}/{season}-{episode}',
            ],
        ],
    ],

];
