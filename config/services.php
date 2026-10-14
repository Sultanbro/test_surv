<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'onepay' => [
        'username' => env('ONEPAY_USERNAME'),
        'password' => env('ONEPAY_PASSWORD'),
        'serviceID' => env('ONEPAY_SERVICEID'),
        'account' => env('ONEPAY_ACCOUNT'),
    ],

    'recaptcha' => [
        'key' => env('GOOGLE_RECAPTCHA_KEY'),
        'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
    ],

    'intellect' => [
        'message_webhook' => env('INTELLECT_MESSAGE_WEBHOOK'),
        'contract_link' => 'https://bpartners.kz/bcontract?hash=',
        'time_link' => 'https://bpartners.kz/btime?hash=',
    ],

    'u-call' => [
        'api_key' => env('UCALL_API_KEY'),
        'app_id' => env('UCALL_APP_ID'),
    ],
];
