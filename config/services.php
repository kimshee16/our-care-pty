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

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'google_drive' => [
        'shared_drive_id' => env('GOOGLE_DRIVE_SHARED_DRIVE_ID'),
        'credentials_path' => env('GOOGLE_DRIVE_CREDENTIALS_PATH'),
        'max_upload_mb' => (int) env('GOOGLE_DRIVE_MAX_UPLOAD_MB', 20),
    ],

    'docusign' => [
        'demo' => env('DOCUSIGN_DEMO', true),
        'integration_key' => env('DOCUSIGN_INTEGRATION_KEY'),
        'user_id' => env('DOCUSIGN_USER_ID'),
        'private_key' => env('DOCUSIGN_PRIVATE_KEY'),
        'account_id' => env('DOCUSIGN_ACCOUNT_ID'),
        'base_path' => env('DOCUSIGN_BASE_PATH', 'https://demo.docusign.net/restapi'),
        'oauth_base_path' => env('DOCUSIGN_OAUTH_BASE_PATH'),
        'consent_redirect_uri' => env('DOCUSIGN_CONSENT_REDIRECT_URI', env('APP_URL', 'http://localhost')),
        'send_email_to_embedded_signers' => env('DOCUSIGN_SEND_EMAIL_TO_EMBEDDED_SIGNERS', true),
        'template_role' => env('DOCUSIGN_TEMPLATE_ROLE', 'Client'),
        'sign_x_position' => env('DOCUSIGN_SIGN_X_POSITION', 420),
        'sign_y_position' => env('DOCUSIGN_SIGN_Y_POSITION', 650),
    ],

];
