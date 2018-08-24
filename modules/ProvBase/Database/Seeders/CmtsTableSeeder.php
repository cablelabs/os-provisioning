<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\Cmts;

class CmtsTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed_l2) as $index) {
            CMTS::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked CMTS data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic)
    {
        $faker = &\NmsFaker::getInstance();

        $company = ['Cisco', 'Casa'];

        $ret = [
            'hostname' => $faker->unique->name,
            'type' => 'cmts',
            'ip' => $faker->localIpv4(),	// using local IPs prevent NMS from snmpget against outside IPs
            'community_rw' => 'private',
            'community_ro' => 'public',
            'company' => $company[array_rand($company)],
            // 'network'
            // 'state'
            // 'monitoring'
        ];

        return $ret;
    }
}
