<?php

return [
    #price for one extra user
    'payment_for_one_person_kzt' => floatval(env('PAYMENT_FOR_ONE_PERSON_KZT', 1000)),
    'payment_for_one_person_rub' => floatval(env('PAYMENT_FOR_ONE_PERSON_RUB', 200)),

    'providers' => [
        'prodamus' => [
            'payment_url' => env('PRODAMUS_SHOP_PAYMENT_URL', 'https://bp.proeducation.kz/'),
            'secret_key' => env('PRODAMUS_SHOP_KEY'),
            'success_url' => 'https://jobtron.org',
            'failed_url' => 'https://jobtron.org',
        ],
        'prodamus-test' => [
            'payment_url' => env('PRODAMUS_SHOP_PAYMENT_URL', 'https://bp.proeducation.kz/'),
            'secret_key' => env('PRODAMUS_SHOP_KEY'),
            'success_url' => 'https://jobtron.org',
            'failed_url' => 'https://jobtron.org',
        ],
        'wallet1' => [
            'payment_url' => env('WALLET1_SHOP_PAYMENT_URL', 'https://wl.walletone.com/checkout/checkout/Index'),
            'merchant_id' => env('WALLET1_SHOP_ID'),
            'shop_key' => env('WALLET1_SHOP_KEY'),
            'success_url' => 'https://jobtron.org',
            'failed_url' => 'https://jobtron.org',
        ],
        'practicum' => [
            'payment_url' => env('PRODAMUS_SHOP_PAYMENT_URL', 'https://proeducation.kz/8g7i8/'),
            'secret_key' => env('PRODAMUS_SHOP_KEY'),
            'success_url' => 'https://jobtron.org',
            'failed_url' => 'https://jobtron.org',
            'gateway' => 'prodamus',
            'provider' => 'practicum'
        ],
    ],
    'gateways' => [
        'prodamus',
        'wallet1'
    ]
];
