<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\NetGw;

class NetGwTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed_l2) as $index) {
            NetGw::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked NetGw data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic)
    {
        $faker = &\NmsFaker::getInstance();

        $conf = config('provbase.netgw');
        $company = array_keys($conf);

        $iter = new RecursiveIteratorIterator(new RecursiveArrayIterator($conf));
        $series = iterator_to_array($iter, false);

        $type = array_values(NetGw::getPossibleEnumValues('type'));

        $ret = [
            'hostname' => $faker->unique->name,
            'type' => $type[array_rand($type)],
            'ip' => $faker->localIpv4(),	// using local IPs prevent NMS from snmpget against outside IPs
            'community_rw' => 'private',
            'community_ro' => 'public',
            'company' => $company[array_rand($company)],
            'series' => $series[array_rand($series)],
            // 'network'
            // 'state'
            // 'monitoring'
        ];

        return $ret;
    }
}
