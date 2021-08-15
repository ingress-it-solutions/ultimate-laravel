<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enabling
    |--------------------------------------------------------------------------
    |
    | Setting "false" the package stop sending data to Ultimate.
    |
    */

    'enable' => env('ULTIMATE_ENABLE', true),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | You can find your API key on your Ultimate project settings page.
    |
    | This API key points the Ultimate notifier to the project in your account
    | which should receive your application's events & exceptions.
    |
    */

    'key' => env('ULTIMATE_API_KEY', env('ULTIMATE_BUGTRAP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Remote URL
    |--------------------------------------------------------------------------
    |
    | You can set the url of the remote endpoint to send data to.
    |
    */

    'url' => env('ULTIMATE_URL', 'https://ingest.ultimate.dev'),

    /*
    |--------------------------------------------------------------------------
    | Transport method
    |--------------------------------------------------------------------------
    |
    | This is where you can set the data transport method.
    | Supported options: "sync", "async"
    |
    */

    'transport' => env('ULTIMATE_TRANSPORT', 'async'),

    /*
    |--------------------------------------------------------------------------
    | Max number of items.
    |--------------------------------------------------------------------------
    |
    | Max number of items to record in a single execution cycle.
    |
    */

    'max_items' => env('ULTIMATE_MAX_ITEMS', 100),

    /*
    |--------------------------------------------------------------------------
    | Proxy
    |--------------------------------------------------------------------------
    |
    | This is where you can set the transport option settings you'd like us to use when
    | communicating with Ultimate.
    |
    */

    'options' => [
        // 'proxy' => 'https://55.88.22.11:3128',
        // 'curlPath' => '/usr/bin/curl',
    ],

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to automatically add all queries executed in the timeline.
    |
    */

    'query' => env('ULTIMATE_QUERY', true),

    /*
    |--------------------------------------------------------------------------
    | Bindings
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to include the query bindings.
    |
    */

    'bindings' => env('ULTIMATE_QUERY_BINDINGS', true),

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to set the current user logged in via
    | Laravel's authentication system.
    |
    */

    'user' => env('ULTIMATE_USER', true),

    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor email sending.
    |
    */

    'email' => env('ULTIMATE_EMAIL', true),

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor notifications.
    |
    */

    'notifications' => env('ULTIMATE_NOTIFICATIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Job
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor background job processing.
    |
    */

    'job' => env('ULTIMATE_JOB', true),

    /*
    |--------------------------------------------------------------------------
    | Job
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor background job processing.
    |
    */

    'redis' => env('ULTIMATE_REDIS', true),

    /*
    |--------------------------------------------------------------------------
    | Exceptions
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to report unhandled exceptions.
    |
    */

    'unhandled_exceptions' => env('ULTIMATE_UNHANDLED_EXCEPTIONS', true),

    /*
 |--------------------------------------------------------------------------
 | Http Client monitoring
 |--------------------------------------------------------------------------
 |
 | Enable this if you'd like us to report the http requests done using the Laravel Http Client.
 |
 */

    'http_client' => env('ULTIMATE_HTTP_CLIENT', true),


    /*
     |--------------------------------------------------------------------------
     | With Server Status
     |--------------------------------------------------------------------------
     |
     | Enable this if you'd like us to report server status information (cpu, ram, hdd).
     |
     */
    'server_sampling_ratio' => env('ULTIMATE_SERVER_SAMPLING_RATIO', 0),


    /*
    |--------------------------------------------------------------------------
    | Hide sensible data from http requests
    |--------------------------------------------------------------------------
    |
    | List request fields that you want mask from the http payload.
    | You can specify nested fields using the dot notation: "user.password"
    */

    'hidden_parameters' => [
        'password',
        'password_confirmation'
    ],

    /*
    |--------------------------------------------------------------------------
    | Artisan command to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list all command signature that you don't want monitoring
    | in your Ultimate dashboard.
    |
    */

    'ignore_commands' => [
        'storage:link',
        'optimize',
        'optimize:clear',
        'schedule:run',
        'schedule:finish',
        'vendor:publish',
        'list',
        'test',
        'package:discover',
        'migrate',
        'migrate:rollback',
        'migrate:refresh',
        'migrate:fresh',
        'migrate:reset',
        'migrate:install',
        'cache:clear',
        'config:cache',
        'config:clear',
        'route:cache',
        'route:clear',
        'view:cache',
        'view:clear',
        'queue:listen',
        'queue:work',
        'queue:restart',
        'vapor:work',
        'horizon',
        'horizon:work',
        'horizon:supervisor',
        'horizon:terminate',
        'horizon:snapshot',
        'nova:publish',
    ],

    /*
    |--------------------------------------------------------------------------
    | Web request url to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list the url schemes that you don't want monitoring
    | in your Ultimate dashboard. You can also use wildcard expression (*).
    |
    */

    'ignore_url' => [
        'telescope*',
        'vendor/telescope*',
        'horizon*',
        'vendor/horizon*',
        'nova*'
    ],

    /*
    |--------------------------------------------------------------------------
    | Job classes to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list the job classes that you don't want monitoring
    | in your Ultimate dashboard.
    |
    */

    'ignore_jobs' => [
        //\App\Jobs\MyJob::class
    ],
];
