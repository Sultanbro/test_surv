<?php
return [
    'token' => env('BITRIX_TOKEN'),
    'host' => env('BITRIX_HOST', 'https://crm.example.invalid/rest/'),
    'notification' => [
        'token' => env('BITRIX_NOTIFICATION_TOKEN'),
        'host' => env('BITRIX_NOTIFICATION_HOST', 'https://crm.example.invalid/rest/'),
    ],
];
