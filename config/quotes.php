<?php

return [
    'form_defaults' => [
        'global' => [
            'vat_rate' => 20,
            'payment_terms' => '50% deposit upfront, balance due within 7 days of completion.',
            'whatsapp_phone' => '+44',
            'google_review_url' => '',
            'feedback_notification_email' => '',
        ],

        'modules' => [
            'fencing' => [
                'length' => 10,
                'labour_rate' => 35.00, // per hour, per metre, or flat rate
                'use_markup' => true,
                'markup' => 15,
                'waste' => 8,
                'type' => 'panels', // boards, panels
                'item_prices' => [
                    'rails'  => ['cost_price' => 8.00,  'sell_price' => 12.00],
                    'boards' => ['cost_price' => 15.00, 'sell_price' => 22.00],
                    'posts'  => ['cost_price' => 5.00,  'sell_price' => 8.00],
                    'panels' => ['cost_price' => 31.00, 'sell_price' => 35.00],
                    'gravel_boards' => ['cost_price' => 10.00, 'sell_price' => 15.00]
                ],
            ],
            'paving' => [
                'length' => 30,
                'labour_rate' => 45,
                'markup' => 18,
                'waste' => 10,
            ],
            'roofing' => [
                'length' => 25,
                'labour_rate' => 55,
                'markup' => 20,
                'waste' => 12,
            ],
        ],
    ],
];
