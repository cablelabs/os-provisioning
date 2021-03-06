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

namespace Modules\HfcReq\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\HfcReq\Entities\NetElement;

class NetElementTableSeeder extends \BaseSeeder
{
    /*
     * TODO: These "helper" functions should be moved to Model context
     */
    // private function type ($t)
    // {
    // 	switch ($t) {
    // 		case '1': return 'NET';
    // 		case '2': return 'NETGW';
    // 		case '3': return 'DATA';
    // 		case '4': return 'CLUSTER';
    // 		case '5': return 'NODE';
    // 		case '6': return 'AMP';

    // 		default:
    // 			return 'AMP';
    // 	}
    // }

    private function state($s)
    {
        switch ($s) {
            case '0': return 'OK';
            case '1': return 'YELLOW';
            case '2': return 'RED';

            default:
                return 'OK';
        }
    }

    private function pos_dumping($netelements)
    {
        foreach ($netelements as $elem) {
            $children = $elem->children;
            $this->pos_dumping($children);

            if (isset($pos) && rand(0, 10) > 7) {
                $elem->pos = $pos;
                $elem->save();

                echo "\r\n change pos of elem with id ".$elem->id;
            }

            $pos = $elem->pos;
        }
    }

    public function run()
    {
        $faker = Faker::create();
        $i = 2;

        foreach (range(1, self::$max_seed) as $index) {
            $x = 13 + $faker->longitude() / 10;
            $y = 50 + $faker->latitude() / 10;

            NetElement::create([
                'name' => $faker->domainWord(),
                'ip' => $faker->localIpv4(),
                'netelementtype_id' => rand(1, 10) > 3 ? 1 : (rand(1, 10) > 3 ? 2 : rand(3, 6)),
                'parent_id' => $index == 1 ? 0 : NetElement::where('id', '>', '1')->get()->random(1)->id,
                'descr' => $faker->sentence(),
                'pos' => $x.','.$y,
                'link' => $faker->url(),
            ]);
        }

        $root = NetElement::find(2);

        // Make top level elements of type NET, second level of type CLUSTER
        foreach ($root->children as $net) {
            $net->netelementtype_id = 1;
            $net->save();

            foreach ($net->children as $cluster) {
                $cluster->netelementtype_id = 2;
                $cluster->save();
            }
        }

        $this->pos_dumping(NetElement::where('netelementtype_id', '=', 1)->get());

        echo "\nATTENTION: disabled call of NetElement::relation_index_build_all(1) in ".__METHOD__;
        echo "\nSee https://devel.roetzer-engineering.com/jira/browse/LAR-179 for details";
        /* NetElement::relation_index_build_all(1); */
    }
}
