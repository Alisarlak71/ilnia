<?php

/*
 * Vercel DEMO entry point (branch vercel-demo only — not used on the real host).
 *
 * On Vercel only /tmp is writable and it is wiped whenever a new instance starts, so:
 * - the demo database (catalog only, no customers) is copied to /tmp on a cold start;
 * - compiled views and framework caches go to /tmp (see the env vars on the Vercel project).
 * Anything a visitor changes (cart, orders, logins) can disappear at any time. That is expected.
 */

$db = '/tmp/ilnia-demo.sqlite';
if (! file_exists($db)) {
    copy(__DIR__.'/../database/demo.sqlite', $db);
}

if (! is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0777, true);
}

require __DIR__.'/../public/index.php';
