<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel-admin database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for laravel-admin builtin model & tables.
    |
    */
    'database' => [
        // Menu table and model.
        'operation_statistic_model' => Xianghuawe\Admin\Models\AdminOperationLogStatistic::class,
    ],

    'notification' => [
        'client_id' => env('ADMIN_NOTIFICATION_CLIENT_ID', ''),
        'client_secret' => env('ADMIN_NOTIFICATION_CLIENT_SECRET', ''),
        'uri' => env('ADMIN_NOTIFICATION_URI', ''),
        'endpoint' => env('ADMIN_NOTIFICATION_ENDPOINT', 'notifications'),
    ],

    'operation-log-statistic' => [
        'enable' => env('OPERATION_LOG_STATISTIC_ENABLE', false),
        'daily_at' => env('OPERATION_LOG_STATISTIC_AT', '09:55'),
    ],

];
