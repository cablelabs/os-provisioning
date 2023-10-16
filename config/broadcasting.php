<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "redis", "log", "null"
    |
    */

    'default' =>  env('BROADCAST_DRIVER', 'pusher-php'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [
        'pusher-php' => [
            'driver' => 'pusher',
            'key' => env('MIX_PUSHER_APP_KEY', 'nmsprime'),
            'secret' => env('PUSHER_APP_SECRET', 'nmsprime'),
            'app_id' => env('PUSHER_APP_ID', 'nmsprime'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => '127.0.0.1',
                'port' => 6001,
                'encrypted' => true,
                'scheme' => env('PUSHER_APP_SCHEME', 'https'),
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ],
            ],
            'client_options' => [
                'verify' => false, // to disable TLS checks
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('MIX_PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => env('APP_URL', '127.0.0.1'),
                'port' => (int) env('MIX_WEBSOCKETS_PORT', env('HTTPS_ADMIN_PORT', 8080)),
                'encrypted' => env('MIX_PUSHER_FORCE_TLS', true),
                'scheme' => env('PUSHER_APP_SCHEME', 'https'),
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
