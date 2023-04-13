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

namespace Modules\ProvBase\Entities;

return  [
    'link' => 'ProvBase.index',
    'MenuItems' => [
        'Contracts' => [
            'link'	=> 'Contract.index',
            'icon'	=> 'fa-address-book-o',
            'class' => Contract::class,
        ],
        'Domains' => [
            'link' 	=> 'Domain.index',
            'icon'	=> 'fa-tag',
            'class' => Domain::class,
        ],
        'Modems' => [
            'link'	=> 'Modem.index',
            'icon'	=> 'fa-hdd-o',
            'class' => Modem::class,
        ],
        'Endpoint' => [
            'link'	=> 'Endpoint.index',
            'icon'	=> 'fa-map-marker',
            'class' => Endpoint::class,
        ],
        'Configfile' => [
            'link'	=> 'Configfile.index',
            'icon'	=> 'fa-file-code-o',
            'class' => Configfile::class,
        ],
        'FirmwareUpgrade' => [
            'link'	=> 'FirmwareUpgrade.index',
            'icon'	=> 'fa-arrow-circle-up',
            'class' => FirmwareUpgrade::class,
        ],
        'Qos' => [
            'link'	=> 'Qos.index',
            'icon'	=> 'fa-ticket',
            'class' => Qos::class,
        ],
        'NetGw' => [
            'link'	=> 'NetGw.index',
            'icon'	=> 'fa-server',
            'class' => NetGw::class,
        ],
        'Ip-Pools' => [
            'link'	=> 'IpPool.index',
            'icon'	=> 'fa-tags',
            'class' => IpPool::class,
        ],
    ],
    'cwmpConnectionRequest' => env('CWMP_CONNECTION_REQUEST', 1),
    'cwmpConnectionRequestTimeout' => env('CWMP_CONNECTION_REQUEST_TIMEOUT', 3000),
    'cwmpMonitoringEvents' => env('CWMP_MONITORING_EVENTS', 2),
    'arrisModem' => [
        'community' => 'cprivate',
        'community_walk' => 'cpublic',
        'downstream' => '1',
        'downstream_stop' => '2',
        'hostname' => env('ARRIS_MODEM_HOSTNAME', 'demo.nmsprime.com'),
        'ip' => env('ARRIS_MODEM_IP', '127.0.0.1'),
        'oids' => [
            'ip' => '.1.3.6.1.4.1.4115.1.20.1.1.24.3.0',
            'protocol' => '.1.3.6.1.4.1.4115.1.20.1.1.24.6.0',
            'port' => '.1.3.6.1.4.1.4115.1.20.1.1.24.7.0',
            'time' => '.1.3.6.1.4.1.4115.1.20.1.1.24.12.0',
            'upstream' => '.1.3.6.1.4.1.4115.1.20.1.1.24.1.0',
            'upstreamMetrics' => '.1.3.6.1.4.1.4115.1.20.1.1.24.9',
            'downstream' => '.1.3.6.1.4.1.4115.1.20.1.1.24.4.0',
            'downstreamMetrics' => '.1.3.6.1.4.1.4115.1.20.1.1.24.10',
        ],
        'protocol' => 'tcp',
        'port' => '5001',
        'time' => '10',
        'upstream' => '1',
    ],
];
