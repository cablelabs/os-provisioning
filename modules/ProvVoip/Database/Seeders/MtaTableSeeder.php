<?php

namespace Modules\ProvVoip\Database\Seeders;

use Modules\ProvVoip\Entities\Mta;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Configfile;

class MtaTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed) as $index) {
            Mta::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked mta data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     * @param $modem modem to create the mta at; used in testing
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $modem = null)
    {
        $faker = &\NmsFaker::getInstance();

        // in seeding mode: choose random modem to create mta at
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

        $ret = [
            'mac' => $faker->macAddress(),
            /* 'type' => (rand(0, 1) == 1 ? 1 : 2), */
            'type' => 'sip',	// only seed sip mta (packetcable is not implemented and therefore may raise problems in testing)
            'modem_id' => $modem_id,
            'configfile_id' => Configfile::where('device', '=', 'mta')->get()->random(1)->id,
        ];

        return $ret;
    }
}
