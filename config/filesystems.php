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
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'erd' => [
            'driver' => 'local',
            'root' => storage_path('app/public/hfcbase/erd'),
            'url' => env('APP_URL').'/storage/hfcbase/erd',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', 'your-key'),
            'secret' => env('AWS_SECRET_ACCESS_KEY', 'your-secret'),
            'region' => env('AWS_DEFAULT_REGION', 'your-region'),
            'bucket' => env('AWS_BUCKET', 'your-bucket'),
        ],

        'rackspace' => [
            'driver'    => 'rackspace',
            'username'  => 'your-username',
            'key'       => 'your-key',
            'container' => 'your-container',
            'endpoint'  => 'https://identity.api.rackspacecloud.com/v2.0/',
            'region'    => 'IAD',
            'url_type'  => 'publicURL',
        ],

        'chart-data' => [
            'driver' => 'local',
            'root' => storage_path('app/data/dashboard'),
        ],

        'tempfs' => [
            'driver' => 'local',
            'root' => '/run/nmsprime',
        ],

        'public-ccc' => [
            'driver' => 'local',
            'root'   => public_path(),
        ],
    ],

];
