<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\Qos;

class QosTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed_l2) as $index) {
            Qos::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked qos data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic)
    {
        $faker = &\NmsFaker::getInstance();

        $count = Qos::withTrashed()->count();
        $ret = [
            'name' => 'QOS-'.$count,
            'ds_rate_max' => rand(1, 100),
            'us_rate_max' => rand(1, 10),
        ];

        return $ret;
    }
}
