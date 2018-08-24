<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\Qos;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Configfile;

class ModemTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed) as $index) {
            Modem::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked modem data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     * @param $contract contract to create the modem at; used in testing
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $contract = null)
    {
        $faker = &\NmsFaker::getInstance();

        // in seeding mode: choose random contract to create modem at
        if ($topic == 'seed') {
            $contract = Contract::all()->random(1);
            $contract_id = $contract->id;
        } else {
            if (! is_null($contract)) {
                $contract_id = $contract->id;
            } else {
                $contract_id = null;
            }
        }

        // randomly decide if contact data shall be derived from contract
        if (rand(0, 10) <= 8) {
            $firstname = $contract->firstname;
            $lastname = $contract->lastname;
            $zip = $contract->zip;
            $city = $contract->city;
            $street = $contract->street;
            $house_number = $contract->house_number;
            $x = $contract->x;
            $y = $contract->y;
            $country_id = $contract->country_id;
        } else {
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $zip = $faker->postcode;
            $city = $faker->city;
            $street = $faker->streetName;
            $house_number = rand(1, 128);
            $x = 13 + $faker->longitude() / 10;
            $y = 50 + $faker->latitude() / 10;
            $country_id = 0;
        }

        $netelement_id = 0;
        if (\Module::collections()->has('HfcReq')) {
            // Note: requires HfcReq to be seeded before this runs
            if (\Modules\HfcReq\Entities\NetElement::all()->count() > 2) {
                $netelement_id = \Modules\HfcReq\Entities\NetElement::where('id', '>', '2')->get()->random(1)->id;
            }
        }

        $ret = [
            'mac' => $faker->macAddress(),
            'description' => $faker->realText(200),
            'network_access' => $faker->boolean(),
            'public' => (rand(0, 100) < 5 ? 1 : 0),
            'serial_num' => $faker->sentence(),
            'inventar_num' => $faker->sentence(),
            'contract_id' => $contract_id,
            'configfile_id' => Configfile::where('device', '=', 'cm')->get()->random(1)->id,
            'qos_id' => Qos::all()->random()->id,
            'netelement_id' => $netelement_id,
            'us_snr' => (rand(0, 10) > 1 ? rand(100, 400) : 0),
            'ds_pwr' => (rand(0, 10) > 1 ? rand(-200, 200) : 0),
            'ds_snr' => (rand(0, 10) > 1 ? rand(100, 500) : 0),
            'us_pwr' => (rand(0, 10) > 1 ? rand(300, 620) : 0),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'zip' => $zip,
            'city' => $city,
            'house_number' => $house_number,
            'country_id' => $country_id,
            'street' => $street,
            'x' => $x,
            'y' => $y,
        ];

        return $ret;
    }
}
