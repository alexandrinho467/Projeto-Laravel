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

    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'correios' => [
        'cep_origem' => env('CORREIOS_CEP_ORIGEM', '86300590'),
        'uf_origem'  => env('CORREIOS_UF_ORIGEM',  'PR'),
        'peso_item'  => env('CORREIOS_PESO_ITEM',  0.6),
    ],

    'porter' => [
        'key'        => env('PORTER_API_KEY', ''),
        'url'        => env('PORTER_API_URL', ''),
        'origin_lat' => env('PORTER_ORIGIN_LAT', 25.0762),
        'origin_lng' => env('PORTER_ORIGIN_LNG', 55.1403),
        'peso_item'  => env('PORTER_PESO_ITEM', 0.6), // kg por par de tênis
    ],

    'whatsapp' => [
        'token'           => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'verify_token'    => env('WHATSAPP_VERIFY_TOKEN'),
        'app_secret'      => env('WHATSAPP_APP_SECRET'),
    ],

    'instagram' => [
        'token'        => env('INSTAGRAM_TOKEN'),
        'ig_user_id'   => env('INSTAGRAM_IG_USER_ID'),
        'verify_token' => env('INSTAGRAM_VERIFY_TOKEN'),
        'app_secret'   => env('INSTAGRAM_APP_SECRET'),
    ],

];
