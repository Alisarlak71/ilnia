<?php

use Illuminate\Support\Facades\Schedule;

/*
| Scheduled tasks. On the host, one cron line runs this every minute:
|   * * * * * cd /path/to/ilnia-shop && php artisan schedule:run >> /dev/null 2>&1
*/

// Delete old one-time login codes (and any other prunable models).
Schedule::command('model:prune')->daily();

// Run queued jobs (e.g. order confirmation SMS). No Supervisor on shared hosting, so the
// scheduler starts a short worker every minute that exits when the queue is empty.
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();

// Cancel unpaid orders older than config('shop.unpaid_order_hours') (default 24 hours).
// Unsettled payments are verified with the gateway first, so a paid order is never cancelled.
Schedule::command('ilnia:cancel-unpaid-orders')->hourly()->withoutOverlapping();
