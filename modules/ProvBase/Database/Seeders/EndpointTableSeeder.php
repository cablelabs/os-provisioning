<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Endpoint;

class EndpointTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed_l2) as $index) {
            Endpoint::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked endpoint data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $modem = null)
    {
        $faker = &\NmsFaker::getInstance();

        if ($topic == 'seed') {
            $modem = Modem::all()->random(1);
            $modem_id = $modem->id;
        } else {
            if (! is_null($modem)) {
                $modem_id = $modem->id;
            } else {
                $modem_id = null;
            }
        }

        if (rand(0, 1) == 1) {
            $fixed_ip = 1;
            $ip = $faker->localIpv4();
        } else {
            $fixed_ip = 0;
            $ip = null;
        }

        $ret = [
            'mac' => $faker->macAddress(),
            'description' => $faker->realText(200),
            'hostname' => $faker->domainWord.$faker->domainWord.$faker->domainWord,
            'modem_id' => $modem_id,
            'fixed_ip' => $fixed_ip,
            'ip' => $ip,
        ];

        return $ret;
    }
}
