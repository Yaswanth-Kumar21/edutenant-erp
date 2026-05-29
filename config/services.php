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

    // ── Razorpay Payment Gateway ──────────────────────────────────────────
    'razorpay' => [
        'key_id'         => env('RAZORPAY_KEY_ID', 'rzp_test_demo'),
        'key_secret'     => env('RAZORPAY_KEY_SECRET', ''),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
    ],

    // ── Twilio SMS ────────────────────────────────────────────────────────
    'twilio' => [
        'enabled' => env('SMS_ENABLED', false),
        'sid'     => env('TWILIO_SID', ''),
        'token'   => env('TWILIO_TOKEN', ''),
        'from'    => env('TWILIO_FROM', ''),
    ],

    // ── WhatsApp (via Twilio) ─────────────────────────────────────────────
    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'from'    => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
    ],

];
