<?php

// Shop settings that are not prices (prices and shipping costs live in the admin panel).

return [
    // Unpaid orders (pending_payment / payment_failed) with no payment attempt for this many
    // hours are cancelled automatically (ilnia:cancel-unpaid-orders, every hour).
    'unpaid_order_hours' => (int) env('UNPAID_ORDER_HOURS', 24),

    // Social accounts shown in the footer: the username only (no @, no link). Empty = not shown,
    // so a page never links to an account that does not exist yet (honesty rule).
    'social' => [
        'instagram' => ltrim((string) env('SOCIAL_INSTAGRAM', ''), '@'),
        'telegram' => ltrim((string) env('SOCIAL_TELEGRAM', ''), '@'),
        'bale' => ltrim((string) env('SOCIAL_BALE', ''), '@'),
    ],
];
