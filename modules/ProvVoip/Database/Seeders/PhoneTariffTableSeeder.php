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

namespace Modules\ProvVoip\Database\Seeders;

use Modules\ProvVoip\Entities\PhoneTariff;

// don't forget to add Seeder in DatabaseSeeder.php
class PhoneTariffTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed) as $index) {
            PhoneTariff::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked phonetariff data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     * @param $mta mta to create the phonetariff at; used in testing
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $mta = null)
    {
        $faker = &\NmsFaker::getInstance();

        $ret = [
            'external_identifier' => $faker->firstName().$faker->lastName().rand(100, 999999),
            'name' => 'Tariff '.$faker->firstName().$faker->lastName().rand(100, 999999),
            'type' => $faker->randomElement(['purchase', 'sale']),
            'usable' => rand(0, 1),
            'description' => $faker->text(50),
            'voip_protocol' => 'SIP',
        ];

        return $ret;
    }
}
