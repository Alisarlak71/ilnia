<?php

use Illuminate\Support\Facades\Schedule;

/*
| Scheduled tasks. On the host, one cron line runs this every minute:
|   * * * * * cd /path/to/ilnia-shop && php artisan schedule:run >> /dev/null 2>&1
*/

// Delete old one-time login codes (and any other prunable models).
Schedule::command('model:prune')->daily();
