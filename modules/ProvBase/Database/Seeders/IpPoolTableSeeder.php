<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\NetGw;
use Modules\ProvBase\Entities\IpPool;

class IpPoolTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed) as $index) {
            IpPool::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked IP pool data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     * @param $netgw NetGw to create the IP pool at; used in testing
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $netgw = null)
    {
        $faker = &\NmsFaker::getInstance();

        // in seeding mode: choose random NetGw to create modem at
        if ($topic == 'seed') {
            $netgw = NetGw::all()->random(1);
            $netgw_id = $netgw->id;
        } else {
            if (! is_null($netgw)) {
                $netgw_id = $netgw->id;
            } else {
                $netgw_id = null;
            }
        }

        $m = $faker->numberBetween(0, 255);
        $n = $faker->numberBetween(0, 255);

        $ret = [
            'netgw_id' => NetGw::all()->random(1)->id,
            'type' => rand(0, 3),
            'net' => '10.'.$m.'.'.$n.'.0',
            'netmask' => '255.255.255.0',
            'ip_pool_start' => '10.'.$m.'.'.$n.'.2',
            'ip_pool_end' => '10.'.$m.'.'.$n.'.253',
            'router_ip' => '10.'.$m.'.'.$n.'.1',		// = netgw ip
            'broadcast_ip' => '10.'.$m.'.'.$n.'.255',
            'dns1_ip' => '10.'.$m.'.'.$n.'.1',
            'dns2_ip' => $faker->localIpv4(),
            'dns3_ip' => $faker->localIpv4(),
            'description' => $faker->sentence(),
        ];

        return $ret;
    }
}
