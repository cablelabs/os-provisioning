<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\ProvBase;

class ProvBaseConfigTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();
        ProvBase::create([
            'provisioning_server' => '172.20.0.1',
            'ro_community' => 'public',
            'rw_community' => 'private',
            'notif_mail' => $faker->email,
            'domain_name' => 'test2.erznet.tv.',
            'dhcp_def_lease_time' => 60,
            'dhcp_max_lease_time' => 120,
            'startid_contract' => 500000,
            'startid_modem' => 100000,
            'startid_endpoint' => 200000,
        ]);
    }
}
