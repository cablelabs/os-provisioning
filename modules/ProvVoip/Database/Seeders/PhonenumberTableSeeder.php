<?php

namespace Modules\ProvVoip\Database\Seeders;

use Modules\ProvVoip\Entities\Mta;
use Modules\ProvVoip\Entities\Phonenumber;

// don't forget to add Seeder in DatabaseSeeder.php
class PhonenumberTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed) as $index) {
            Phonenumber::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked phonenumber data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     * @param $mta mta to create the phonenumber at; used in testing
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $mta = null)
    {
        $faker = &\NmsFaker::getInstance();

        // in seeding mode: choose random mta to create phonenumber at
        if ($topic == 'seed') {
            $mta = Mta::all()->random(1);
            $mta_id = $mta->id;
        } else {
            if (! is_null($mta)) {
                $mta_id = $mta->id;
            } else {
                $mta = Mta::all()->random(1)->get();
                $mta_id = $mta->id;
            }
        }

        // check for ports already taken on this mta
        // this field is unique but not checked in Phonenumber::$rules which can crash the unit tests
        $ports_taken = [];
        foreach (\DB::table('phonenumber')->where('mta_id', '=', $mta_id)->whereNull('deleted_at')->get() as $nr_data) {
            array_push($ports_taken, $nr_data->port);
        }

        $port = null;
        $candidate = 1;
        while (is_null($port)) {
            if (! in_array($candidate, $ports_taken)) {
                $port = $candidate;
            }
            $candidate++;
        }

        $ret = [
            'prefix_number' => '0'.rand(2, 9).rand(0, 9999),
            'number' => rand(100, 999999),
            'mta_id' => $mta_id,
            'port' => $port,
            'active' => rand(0, 1),
        ];

        return $ret;
    }
}
