<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\Cmts;
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
     * @param $cmts CMTS to create the IP pool at; used in testing
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $cmts = null)
    {
        $faker = &\NmsFaker::getInstance();

        // in seeding mode: choose random CMTS to create modem at
        if ($topic == 'seed') {
            $cmts = CMTS::all()->random(1);
            $cmts_id = $cmts->id;
        } else {
            if (! is_null($cmts)) {
                $cmts_id = $cmts->id;
            } else {
                $cmts_id = null;
            }
        }

        $m = $faker->numberBetween(0, 255);
        $n = $faker->numberBetween(0, 255);

        $ret = [
            'cmts_id' => CMTS::all()->random(1)->id,
            'type' => rand(0, 3),
            'net' => '10.'.$m.'.'.$n.'.0',
            'netmask' => '255.255.255.0',
            'ip_pool_start' => '10.'.$m.'.'.$n.'.2',
            'ip_pool_end' => '10.'.$m.'.'.$n.'.253',
            'router_ip' => '10.'.$m.'.'.$n.'.1',		// = cmts ip
            'broadcast_ip' => '10.'.$m.'.'.$n.'.255',
            'dns1_ip' => '10.'.$m.'.'.$n.'.1',
            'dns2_ip' => $faker->localIpv4(),
            'dns3_ip' => $faker->localIpv4(),
            'description' => $faker->sentence(),
        ];

        return $ret;
    }
}
