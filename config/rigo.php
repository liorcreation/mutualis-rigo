<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification channels
    |--------------------------------------------------------------------------
    |
    | Channels can be changed without touching the notification classes.
    | Supported values here are the native Laravel "database" and "mail".
    |
    */
    'notification_channels' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('RIGO_NOTIFICATION_CHANNELS', 'database,mail'))
    ))),
];
