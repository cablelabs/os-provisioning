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

namespace Modules\ProvBase\Observers;

use Modules\ProvBase\Entities\NetGW;
use Modules\ProvBase\Entities\ProvBase;

/**
 * IP-Pool Observer Class
 * Handles changes on IP-Pools
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class IpPoolObserver
{
    public function created($pool)
    {
        self::updateRadIpPool($pool);

        if ($pool->netgw->type != 'cmts') {
            return;
        }

        // fetch netgw object that is related to the created ippool and make dhcp conf
        $pool->netgw->makeDhcpConf();

        self::rebuildDhcpGlobalConfig($pool);
    }

    public function updated($pool)
    {
        self::updateRadIpPool($pool);

        if ($pool->netgw->type != 'cmts') {
            return;
        }

        $pool->netgw->makeDhcpConf();

        // make dhcp conf of old netgw if relation got changed
        if ($pool->isDirty('netgw_id')) {
            NetGw::find($pool->getOriginal('netgw_id'))->makeDhcpConf();
        }

        self::rebuildDhcpGlobalConfig($pool);
    }

    public function deleted($pool)
    {
        self::updateRadIpPool($pool);

        if ($pool->netgw->type != 'cmts') {
            return;
        }

        $pool->netgw->makeDhcpConf();

        self::rebuildDhcpGlobalConfig($pool);
    }

    /**
     * Handle changes of radippool based on ippool
     * This is called on created/updated/deleted in IpPool observer
     *
     * @author Ole Ernst
     */
    private static function updateRadIpPool($pool)
    {
        \Queue::pushOn('medium', new \Modules\ProvBase\Jobs\RadIpPoolJob($pool, $pool->getDirty(), $pool->getOriginal(), $pool->wasRecentlyCreated));
    }

    /**
     * Rebuild DHCP global.conf if needed
     * This is called on created/updated/deleted in IpPool observer
     *
     * @author Ole Ernst
     */
    private static function rebuildDhcpGlobalConfig($pool)
    {
        if (($pool->deleted_at && $pool->type == 'STB') || multi_array_key_exists(['type', 'vendor_class_identifier'], $pool->getDirty())) {
            ProvBase::first()->make_dhcp_glob_conf();
        }
    }
}
