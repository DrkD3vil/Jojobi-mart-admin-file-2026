<?php

return [
    // The single physical location this storefront fulfils online orders
    // from (see locations table -- id 1 is the only location today).
    'location_id' => (int) env('STORE_LOCATION_ID', 1),

    'name' => env('APP_NAME', 'JOJOBI MART'),

    'currency_symbol' => env('STORE_CURRENCY_SYMBOL', '৳'),

    'payment_methods' => [
        'cod' => ['label' => 'Cash on delivery', 'channel' => 'offline', 'method' => 'cash'],
        'bkash' => ['label' => 'bKash', 'channel' => 'online', 'method' => 'bkash'],
        'nagad' => ['label' => 'Nagad', 'channel' => 'online', 'method' => 'nagad'],
        'rocket' => ['label' => 'Rocket', 'channel' => 'online', 'method' => 'rocket'],
    ],
];
