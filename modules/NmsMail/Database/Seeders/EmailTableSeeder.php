<?php

namespace Modules\NmsMail\Database\Seeders;

use Faker\Factory as Faker;
use Modules\NmsMail\Entities\Email;
use Modules\ProvBase\Entities\Domain;
use Modules\ProvBase\Entities\Contract;

class EmailTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, self::$max_seed) as $index) {
            $contract = Contract::all()->random(1);

            $email = Email::create([
                'contract_id' => $contract->id,
                'domain_id' => Domain::where('type', '=', 'Email')->get()->random(1)->id,
                'localpart' => $faker->userName(),
                'index' => rand(0, $contract->get_email_count()),
                'greylisting' => rand(0, 1),
                'blacklisting' => rand(0, 1),
                'forwardto' => $faker->email(),
            ]);
            $email->psw_update($faker->password());
        }
    }
}
