<?php

namespace Modules\ProvBase\Observers;

use Modules\ProvBase\Entities\NetGW;

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
    }

    public function deleted($pool)
    {
        self::updateRadIpPool($pool);

        if ($pool->netgw->type != 'cmts') {
            return;
        }

        $pool->netgw->makeDhcpConf();
    }

    /**
     * Handle changes of radippool based on ippool
     * This is called on created/updated/deleted in IpPool observer
     *
     * @author Ole Ernst
     */
    private static function updateRadIpPool($pool)
    {
        \Queue::push(new \Modules\ProvBase\Jobs\RadIpPoolJob($pool, $pool->getDirty(), $pool->getOriginal(), $pool->wasRecentlyCreated));
    }
}
