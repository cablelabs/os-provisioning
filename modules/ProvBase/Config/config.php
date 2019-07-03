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
        'DocumentTemplates' => [
            'link'	=> 'DocumentTemplate.index',
            'icon'	=> 'fa-window-restore',
            'class' => DocumentTemplate::class,
        ],
    ],
    'cwmpConnectionRequest' => env('CWMP_CONNECTION_REQUEST', 1),
    'cwmpConnectionRequestTimeout' => env('CWMP_CONNECTION_REQUEST_TIMEOUT', 3000),
    'cwmpMonitoringEvents' => env('CWMP_MONITORING_EVENTS', 2),
];
