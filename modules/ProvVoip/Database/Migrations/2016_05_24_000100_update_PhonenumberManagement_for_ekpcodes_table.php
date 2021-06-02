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
class UpdatePhonenumberManagementForEkpCodesTable extends BaseMigration
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
            $table->integer('carrier_in')->unsigned()->change();
            $table->integer('carrier_out')->unsigned()->change();

            $table->integer('ekp_in')->unsigned()->after('carrier_in');
            $table->integer('ekp_out')->unsigned()->after('carrier_out');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn([
                'ekp_in',
                'ekp_out',
            ]);

            $table->string('carrier_in', 16)->change();
            $table->string('carrier_out', 16)->change();
        });
    }
}
