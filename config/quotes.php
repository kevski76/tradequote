<?php

return [
    'form_defaults' => [
        'global' => [
            'length' => 15,
            'labour_rate' => 35,
            'markup' => 15,
            'waste' => 8,
            'vat_rate' => 20,
            'payment_terms' => '50% deposit upfront, balance due within 7 days of completion.',
            'whatsapp_phone' => '+44',
        ],

        'modules' => [
            'fencing' => [
                'length' => 15,
                'labour_rate' => 35,
                'markup' => 15,
                'waste' => 8,
                'vat_rate' => 20,
                'payment_terms' => '50% deposit upfront, balance due within 7 days of completion.',
                'whatsapp_phone' => '+44',
            ],
            'paving' => [
                'length' => 30,
                'labour_rate' => 45,
                'markup' => 18,
                'waste' => 10,
                'vat_rate' => 20,
                'payment_terms' => '40% deposit upfront, balance due on completion.',
                'whatsapp_phone' => '+44',
            ],
            'roofing' => [
                'length' => 25,
                'labour_rate' => 55,
                'markup' => 20,
                'waste' => 12,
                'vat_rate' => 20,
                'payment_terms' => '40% deposit upfront, interim payment at materials delivery, balance on completion.',
                'whatsapp_phone' => '+44',
            ],
        ],
    ],
];
