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

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\ProvBase\Entities\IpPool;
use Modules\ProvBase\Entities\RadIpPool;

class RadIpPoolJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    protected const SLICE_SIZE = 1000;

    protected $pool;
    protected $dirty;
    protected $original;
    protected $wasRecentlyCreated;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(IpPool $pool, array $dirty, array $original, bool $wasRecentlyCreated, array $fixedEndpointIps = null)
    {
        $this->pool = $pool;
        $this->dirty = $dirty;
        $this->original = $original;
        $this->wasRecentlyCreated = $wasRecentlyCreated;
        if (is_null($fixedEndpointIps)) {
            $this->fixedEndpointIps = array_map('ip2long', \DB::table('endpoint')->whereNull('deleted_at')->pluck('ip')->toArray());
        } else {
            $this->fixedEndpointIps = $fixedEndpointIps;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->pool || $this->pool->netgw->type != 'bras' || ($this->pool->type != 'CPEPriv' && $this->pool->type != 'CPEPub')) {
            return;
        }

        // add a new ip pool
        if ($this->wasRecentlyCreated) {
            $this->handleNewLimit(ip2long($this->pool->ip_pool_end) + 1, ip2long($this->pool->ip_pool_start), false);

            return;
        }

        // delete all ip addresses of pool
        if ($this->pool->deleted_at) {
            $this->handleNewLimit(ip2long($this->pool->ip_pool_start), ip2long($this->pool->ip_pool_end) + 1, true);

            return;
        }

        // handle change of type (CPEPriv, CPEPub)
        if (array_key_exists('type', $this->dirty)) {
            $org = array_map('ip2long', [
                $this->original['ip_pool_start'],
                $this->original['ip_pool_end'],
            ]);

            $update = [];
            for ($i = $org[0]; $i <= $org[1]; $i++) {
                $update[] = long2ip($i);

                // staggered update
                if (count($update) > self::SLICE_SIZE) {
                    RadIpPool::whereIn('framedipaddress', $update)->update(['pool_name' => $this->pool->type]);
                    $update = [];
                }
            }
            RadIpPool::whereIn('framedipaddress', $update)->update(['pool_name' => $this->pool->type]);
        }

        // handle change of start ip address
        if (array_key_exists('ip_pool_start', $this->dirty)) {
            $this->handleNewLimit(ip2long($this->original['ip_pool_start']), ip2long($this->pool->ip_pool_start), false);
        }

        // handle change of end ip address
        if (array_key_exists('ip_pool_end', $this->dirty)) {
            $this->handleNewLimit(ip2long($this->pool->ip_pool_end) + 1, ip2long($this->original['ip_pool_end']) + 1, false);
        }
    }

    /**
     * Insert and delete rows in radippool table if the start or end of an
     * ippool has changed. This function can also be used to delete a whole pool
     *
     * Note:
     *      if ($ipPoolBorder1 < $ipPoolBorder2) => delete
     *      if ($ipPoolBorder1 > $ipPoolBorder2) => insert
     *
     * @author Ole Ernst
     */
    private function handleNewLimit(int $ipPoolBorder1, int $ipPoolBorder2, bool $deleteOnly): void
    {
        $delete = [];
        for ($i = $ipPoolBorder1; $i < $ipPoolBorder2; $i++) {
            $delete[] = long2ip($i);

            // staggered delete
            if (count($delete) > self::SLICE_SIZE) {
                RadIpPool::whereIn('framedipaddress', $delete)->delete();
                $delete = [];
            }
        }
        RadIpPool::whereIn('framedipaddress', $delete)->delete();

        if ($deleteOnly) {
            RadIpPool::where('framedipaddress', $ipPoolBorder2)->delete();

            return;
        }

        $insert = [];
        for ($i = $ipPoolBorder2; $i < $ipPoolBorder1; $i++) {
            // do not add IPs taken by endpoints
            if (in_array($i, $this->fixedEndpointIps)) {
                continue;
            }
            $insert[] = [
                'pool_name' => $this->pool->type,
                'framedipaddress' => long2ip($i),
                'expiry_time' => '1900-01-01 00:00:00',
            ];

            // staggered insert
            if (count($insert) > self::SLICE_SIZE) {
                RadIpPool::insert($insert);
                $insert = [];
            }
        }
        RadIpPool::insert($insert);
    }
}
