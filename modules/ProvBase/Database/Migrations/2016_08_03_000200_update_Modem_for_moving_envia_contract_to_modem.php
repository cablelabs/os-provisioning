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
 * envia TEL contracts match our Modems (=telephone connection) we move external contract stuff to modem…
 *
 * @author Patrick Reichel
 */
class UpdateModemForMovingEnviaContractToModem extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('contract_external_id')->nullable()->default(null)->after('contract_id');
            $table->date('contract_ext_creation_date')->nullable()->default(null)->after('contract_external_id');
            $table->date('contract_ext_termination_date')->nullable()->default(null)->after('contract_ext_creation_date');
        });

        // give all cols to be indexed (old and new ones => the index will be dropped and then created from scratch)
        $this->set_fim_fields([
            'name',
            'hostname',
            'contract_external_id',
            'firstname',
            'lastname',
            'street',
            'house_number',
            'zip',
            'city',
            'district',
            'birthday',
            'company',
            'department',
            'mac',
            'serial_num',
            'inventar_num',
            'description',
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
            $table->dropColumn([
                'contract_external_id',
                'contract_ext_creation_date',
                'contract_ext_termination_date',
            ]);
        });

        // give all cols to be indexed (old and new ones => the index will be dropped and then created from scratch)
        $this->set_fim_fields([
            'name',
            'hostname',
            'firstname',
            'lastname',
            'street',
            'house_number',
            'zip',
            'city',
            'district',
            'birthday',
            'company',
            'department',
            'mac',
            'serial_num',
            'inventar_num',
            'description',
        ]);
    }
}
