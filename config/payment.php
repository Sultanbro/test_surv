<?php

return [
    #Payment price for one extra user
    'payment_for_one_person_kzt' => floatval(env('PAYMENT_FOR_ONE_PERSON', 980)),
    'payment_for_one_person_rub' => floatval(env('PAYMENT_FOR_ONE_PERSON', 200)),

    'prodamus' => [
//        'payment_url' => env('PRODAMUS_SHOP_PAYMENT_URL', 'https://bp.payform.ru/'),
        'payment_url' => env('PRODAMUS_SHOP_PAYMENT_URL', 'https://bp.proeducation.kz/'),
        'secret_key' => env('PRODAMUS_SHOP_KEY', 'ce5169b490209093ba24359f2beb6cf6b0914badc326c7788528645dd1fe6859'),
        'success_url' => 'https://exmaple.com',
        'failed_url' => 'https://exmaple.com',
    ],
    'wallet1' => [
        'payment_url' => env('WALLET1_SHOP_PAYMENT_URL', 'https://wl.walletone.com/checkout/checkout/Index'),
        'merchant_id' => env('WALLET1_SHOP_ID', 164796334920),
        'shop_key' => env('WALLET1_SHOP_KEY', "164796334920"),
        'success_url' => 'https://exmaple.com',
        'failed_url' => 'https://exmaple.com',
    ]
];