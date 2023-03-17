<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\NmsMail\Database\Seeders;

use Faker\Factory as Faker;
use Modules\NmsMail\Entities\Email;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Domain;

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
