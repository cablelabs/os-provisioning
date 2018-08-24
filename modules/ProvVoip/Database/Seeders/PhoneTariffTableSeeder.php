<?php

namespace Modules\ProvVoip\Database\Seeders;

use Modules\ProvVoip\Entities\PhoneTariff;

// don't forget to add Seeder in DatabaseSeeder.php
class PhoneTariffTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed) as $index) {
            PhoneTariff::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked phonetariff data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     * @param $mta mta to create the phonetariff at; used in testing
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $mta = null)
    {
        $faker = &\NmsFaker::getInstance();

        $ret = [
            'external_identifier' => $faker->firstName().$faker->lastName().rand(100, 999999),
            'name' => 'Tariff '.$faker->firstName().$faker->lastName().rand(100, 999999),
            'type' => $faker->randomElement(['purchase', 'sale']),
            'usable' => rand(0, 1),
            'description' => $faker->text(50),
            'voip_protocol' => 'SIP',
        ];

        return $ret;
    }
}
