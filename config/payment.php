<?php

return [
    #Payment price for one extra user
    'payment_for_one_person' => floatval(env('PAYMENT_FOR_ONE_PERSON')),

    'prodamus' => [
        'shop_id' => env('PRODAMUS_SHOP_ID', 361),
        'shop_key' => env('PRODAMUS_SHOP_KEY', 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d'),
        'success_url' => '',
        'failed_url' => '',
    ],
    'wallet1' => [
        'merchant_id' => env('WALLET1_SHOP_ID', 164796334920),
        'success_url' => '',
        'failed_url' => '',
    ]
];