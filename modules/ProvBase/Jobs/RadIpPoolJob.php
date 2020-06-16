<?php

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Modules\ProvBase\Entities\IpPool;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\ProvBase\Entities\RadIpPool;
use Illuminate\Contracts\Queue\ShouldQueue;

class RadIpPoolJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    const SLICE_SIZE = 1000;

    protected $pool;
    protected $dirty;
    protected $original;
    protected $wasRecentlyCreated;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(IpPool $pool, array $dirty, array $original, bool $wasRecentlyCreated)
    {
        $this->pool = $pool;
        $this->dirty = $dirty;
        $this->original = $original;
        $this->wasRecentlyCreated = $wasRecentlyCreated;
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
     * @author Ole Ernst
     */
    private function handleNewLimit(int $lower, int $upper, bool $deleteOnly): void
    {
        $delete = [];
        for ($i = $lower; $i < $upper; $i++) {
            $delete[] = long2ip($i);

            // staggered delete
            if (count($delete) > self::SLICE_SIZE) {
                RadIpPool::whereIn('framedipaddress', $delete)->delete();
                $delete = [];
            }
        }
        RadIpPool::whereIn('framedipaddress', $delete)->delete();

        if ($deleteOnly) {
            RadIpPool::where('framedipaddress', $upper)->delete();

            return;
        }

        $insert = [];
        for ($i = $upper; $i < $lower; $i++) {
            $insert[] = [
                'pool_name' => $this->pool->type,
                'framedipaddress' => long2ip($i),
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
