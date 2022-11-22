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
            'domain_name' => 'test2.erznet.tv.',
            'dhcp_def_lease_time' => 60,
            'dhcp_max_lease_time' => 120,
            'startid_contract' => 500000,
            'startid_modem' => 100000,
            'startid_endpoint' => 200000,
        ]);
    }
}
