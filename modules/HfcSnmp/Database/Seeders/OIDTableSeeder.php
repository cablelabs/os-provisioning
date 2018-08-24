<?php

namespace Modules\HfcSnmp\Database\Seeders;

use Faker\Factory as Faker;
use Modules\HfcSnmp\Entities\OID;

class OIDTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            OID::create([
                'name' 		=> $faker->colorName,
                'mibfile_id' => rand(1, 5),
                'html_type' => $this->_get_enum('html_type'),
                'type' 		=> $this->_get_enum('type'),
                'oid' 		=> '.1.3.6.1.4.1.'.$faker->localIpv4,
                'oid_table' => rand(1, 20) > 17,
                'syntax' 	=> $this->_get_enum('syntax'),
                'access' 	=> $this->_get_enum('acccess'),
            ]);
        }
    }

    private function _get_enum($field)
    {
        switch ($field) {
            case 'html_type':
                $enum = ['text', 'select', 'slider', 'groupbox', 'textarea'];
                break;

            case 'type':
                $enum = ['i', 'u', 's', 'x', 'd', 'n', 'o', 't', 'a', 'b'];
                break;

            case 'syntax':
                $enum = ['INTEGER', 'Integer32', 'Counter64', 'OCTET STRING', 'NetworkAddress', 'Unsigned32', 'BITS'];
                break;

            case 'acccess':
                $enum = ['read-only', 'read-write', 'accessible-for-notify', 'not-accessible', 'read-create'];
                break;
        }

        $i = rand(0, count($enum) - 1);

        return $enum[$i];
    }
}
