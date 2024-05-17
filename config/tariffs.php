<?php

return [
    'free' => [
        'id' => 1,
        'users_limit' => 5,
        'prices' => [
            'kzt' => 0,
            'rub' => 0,
        ],
        'sale_percent' => 0,
    ],
    'base' => [
        'id' => 2,
        'users_limit' => 20,
        'prices' => [
            'kzt' => 7158,
            'rub' => 1827,
        ],
        'sale_percent' => 20,
    ],
    'standard' => [
        'id' => 3,
        'users_limit' => 50,
        'prices' => [
            'kzt' => 22966,
            'rub' => 5863,
        ],
        'sale_percent' => 20,
    ],
    'pro' => [
        'id' => 4,
        'users_limit' => 100,
        'prices' => [
            'kzt' => 85004,
            'rub' => 21073,
        ],
        'sale_percent' => 20, // per year
    ],
];