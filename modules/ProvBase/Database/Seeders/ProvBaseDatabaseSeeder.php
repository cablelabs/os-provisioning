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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ProvBaseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('Modules\ProvBase\Database\Seeders\NetGwTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\IpPoolTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ConfigfileTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\QosTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ContractTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ModemTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\EndpointTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ProvBaseConfigTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\DomainTableSeeder');
    }
}
