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

    // Demo sites only (e.g. the Vercel demo): show social/support links that are not set yet, with a
    // "#" link, even in production, so the design can be seen. Keep false on the real site (honesty rule).
    'placeholder_links' => (bool) env('SHOW_PLACEHOLDER_LINKS', false),

    // Floating support button (bottom right): WhatsApp mobile number (any format, e.g. 0912…),
    // Telegram and Bale usernames (no @). Empty = that link is not shown on the live site; if all
    // are empty the button is hidden. Outside production empty ones show with a "#" link (design preview).
    'support' => [
        'whatsapp' => (string) env('SUPPORT_WHATSAPP', ''),
        'telegram' => ltrim((string) env('SUPPORT_TELEGRAM', ''), '@'),
        'bale' => ltrim((string) env('SUPPORT_BALE', ''), '@'),
    ],
];
