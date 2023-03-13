<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /**
     * This config is use for enable or not a-ray push
     */
    'enabled' => env('A_RAY_ENABLED', true),

    'api_health' => [
        /**
         * This config is use for enable or not api health check
         */
        'enabled' => env('A_RAY_API_HEALTH_ENABLED', false),

        /**
         * This config is use for set cron for api health check (default 5 minutes)
         */
        'cron' => env('A_RAY_API_HEALTH_CRON', '*/5 * * * *'),

        /**
         * This config is use for set an array of route without get request & response
         */
        'routes_without_request_response' => [
            'api.auth.me'
        ],
        /**
         * This config is use for set an array of field on request for replace this field by ********
         */
        'except_fields' => [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'new_password_confirmation',
        ],
    ],


    /**
     * This config is use for set private key of project in a-ray
     */
    'private_key' => env('A_RAY_PRIVATE_KEY', ''),

    'notify_errors' => [
        /**
         * This config is use for enable or not notify errors
         */
        'enabled' => env('A_RAY_NOTIFY_ERRORS_ENABLED', false),

        /**
         * This config is use for set notify errors level
         */
        'level' => env('A_RAY_NOTIFY_ERRORS_LEVEL', 'error'),

        /**
         * Slack webhook url
         */
        'channel' => env('A_RAY_NOTIFY_CHANNEL', 'slack'),
    ],
];
