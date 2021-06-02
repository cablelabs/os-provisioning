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

namespace Modules\HfcSnmp\Database\Seeders;

use Faker\Factory as Faker;
use Modules\HfcSnmp\Entities\Parameter;

class ParameterTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 4) as $index) {
            foreach (range(1, rand(2, 10)) as $id) {
                Parameter::create([
                    'netelementtype_id' => $index,
                    'oid_id' 			=> rand(1, 50),
                    'html_frame' 		=> rand(1, 10) > 5 ? rand(1, 9) : rand(11, 89),
                    'html_id' 			=> rand(1, 6),
                ]);
            }
        }
    }
}
