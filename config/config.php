<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /**
     * This config is use for enable or not a-ray push
     */
    'enabled' => env('A_RAY_ENABLED', true),

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
