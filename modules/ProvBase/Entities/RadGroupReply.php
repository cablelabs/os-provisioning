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

use Modules\ProvBase\Observers\QosObserver;

class RadGroupReply extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'radgroupreply';
    protected $connection = 'pgsql-radius';

    public $timestamps = false;
    protected $forceDeleting = true;

    // this is 0 since nmsprime.qos.id can never be that value
    public static $defaultGroup = 0;

    // https://wiki.mikrotik.com/wiki/Manual:RADIUS_Client
    // https://help.ubnt.com/hc/en-us/articles/204977464-EdgeRouter-PPPoE-Server-Rate-Limiting-Using-WISPr-RADIUS-Attributes
    public static $radiusAttributes = [
        'ds_rate_max_help' => [
            ['Ascend-Xmit-Rate', ':=', '%d', '%'],
            ['WISPr-Bandwidth-Max-Down', ':=', '%d', '%'],
            ['Cisco-Avpair', '+=', 'ip:qos-policy-out=add-class(sub,(class-default),police(%s))', 'ip:qos-policy-out=%'],
        ],
        'us_rate_max_help' => [
            ['Ascend-Data-Rate', ':=', '%d', '%'],
            ['WISPr-Bandwidth-Max-Up', ':=', '%d', '%'],
            ['Cisco-Avpair', '+=', 'ip:qos-policy-in=add-class(sub,(class-default),police(%s))', 'ip:qos-policy-in=%'],
        ],
        'ds_name' => [
            ['Cisco-Avpair', '+=', 'ip:sub-qos-policy-out=%s', 'ip:sub-qos-policy-out=%'],
        ],
        'us_name' => [
            ['Cisco-Avpair', '+=', 'ip:sub-qos-policy-in=%s', 'ip:sub-qos-policy-in=%'],
        ],
    ];

    // freeradius-mysql does not use softdeletes
    public static function bootSoftDeletes()
    {
    }

    /**
     * Truncate radgroupreply table and refresh all entries - corresponds to Qos
     */
    public static function repopulateDb()
    {
        RadGroupReply::truncate();

        $provbase = ProvBase::first();

        $insert = [
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Port-Limit', 'op' => ':=', 'value' => '1'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Framed-MTU', 'op' => ':=', 'value' => '1492'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Framed-Protocol', 'op' => ':=', 'value' => 'PPP'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Service-Type', 'op' => ':=', 'value' => 'Framed-User'],
            ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Acct-Interim-Interval', 'op' => ':=', 'value' => $provbase->acct_interim_interval],
        ];

        if ($sessionTimeout = $provbase->ppp_session_timeout) {
            $insert[] = ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Session-Timeout', 'op' => ':=', 'value' => $sessionTimeout];
        }

        // this (Fall-Through) MUST be the last entry of $defaultGroup
        $insert[] = ['groupname' => RadGroupReply::$defaultGroup, 'attribute' => 'Fall-Through', 'op' => '=', 'value' => 'Yes'];
        RadGroupReply::insert($insert);

        $observer = new QosObserver;
        foreach (Qos::all() as $qos) {
            $observer->created($qos);
        }
    }
}
