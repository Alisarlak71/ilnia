<?php

// Shop settings that are not prices (prices and shipping costs live in the admin panel).

return [
    // Unpaid orders (pending_payment / payment_failed) with no payment attempt for this many
    // hours are cancelled automatically (ilnia:cancel-unpaid-orders, every hour).
    'unpaid_order_hours' => (int) env('UNPAID_ORDER_HOURS', 24),
];
