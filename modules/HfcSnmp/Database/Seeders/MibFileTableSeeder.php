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
use Modules\HfcSnmp\Entities\MibFile;

class MibFileTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $index) {
            $name = 'DOCS-FAKE-'.$faker->colorName.'-MIB';
            $vers = $faker->year.$faker->month.$faker->dayOfMonth.'0000Z';

            MibFile::create([
                'name' => $name,
                'version' => $vers,
                'filename' => $name.'_'.$vers,
            ]);
        }
    }
}
