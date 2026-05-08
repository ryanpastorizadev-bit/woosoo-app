<?php

return [
    'context_gateway' => env('POS_CONTEXT_GATEWAY', 'fake'),

    'cache' => [
        'context_key' => env('POS_CONTEXT_CACHE_KEY', 'krypton.context'),
        'context_ttl_seconds' => (int) env('POS_CONTEXT_CACHE_TTL', 30),
    ],

    'terminal_id' => env('POS_TERMINAL_ID'),

    'fake' => [
        'readiness' => [
            'session' => [
                'id' => '123',
                'openedAt' => '2026-05-08T10:00:00+08:00',
                'status' => 'open',
            ],
            'terminal' => [
                'id' => '1',
                'name' => 'Main POS',
                'status' => 'open',
            ],
            'blockingReason' => null,
        ],
        'tables' => [
            [
                'id' => '12',
                'name' => 'Table 12',
                'rawStatus' => 'AVAILABLE',
                'isAvailable' => true,
                'isLocked' => false,
            ],
        ],
    ],
];
