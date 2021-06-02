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

use Illuminate\Database\Schema\Blueprint;

/**
 * Updater to add envia related data to contract
 *
 * @author Patrick Reichel
 */
class UpdatePhonenumberManagementForSubscriberDistrict extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonenumbermanagement';

    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('subscriber_district')->after('subscriber_city')->nullable()->default(null);
        });

        $this->set_fim_fields([
            'subscriber_company',
            'subscriber_department',
            'subscriber_firstname',
            'subscriber_lastname',
            'subscriber_street',
            'subscriber_house_number',
            'subscriber_zip',
            'subscriber_city',
            'subscriber_district',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('subscriber_district');
        });

        $this->set_fim_fields([
            'subscriber_company',
            'subscriber_firstname',
            'subscriber_lastname',
            'subscriber_street',
            'subscriber_house_number',
            'subscriber_zip',
            'subscriber_city',
        ]);
    }
}
