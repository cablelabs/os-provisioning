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

use Modules\ProvVoip\Entities\PhoneTariff;

class UpdatePhoneTariffExtendType extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonetariff';

    /**
     * Run the migrations.
     *
     * @author Nino Ryschawy
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE phonetariff MODIFY COLUMN type ENUM('purchase', 'sale', 'basic', 'landlineflat', 'allnetflat') NOT NULL");

        $phonetariffs = [
            0 => [
                'external_identifier' => 1,
                'name' => 'Basic',
                'type' => 'basic',
                'usable' => 1,
            ],
            1 => [
                'external_identifier' => 2,
                'name' => 'Landline flat',
                'type' => 'landlineflat',
                'usable' => 1,
            ],
            2 => [
                'external_identifier' => 3,
                'name' => 'Allnetflat',
                'type' => 'allnetflat',
                'usable' => 1,
            ],
        ];

        if (! PhoneTariff::count()) {
            foreach ($phonetariffs as $pt) {
                PhoneTariff::create($pt);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @author Nino Ryschawy
     *
     * @return void
     */
    public function down()
    {
        PhoneTariff::whereIn('type', ['basic', 'landlineflat', 'allnetflat'])->delete();

        DB::statement("ALTER TABLE phonetariff MODIFY COLUMN type ENUM('purchase', 'sale') NOT NULL");
    }
}
