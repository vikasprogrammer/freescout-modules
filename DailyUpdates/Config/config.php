<?php

return [
    'name' => 'DailyUpdates',
    'process_cron' => env('WORKFLOWS_PROCESS_CRON', '0 * * * *'),
];
