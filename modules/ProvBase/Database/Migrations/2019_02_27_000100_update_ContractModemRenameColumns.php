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

/**
 * Rename network_access to internet_access and telephony_only to has_telephony
 *
 * @author Nino Ryschawy
 */
use Illuminate\Database\Schema\Blueprint;

class UpdateContractModemRenameColumns extends BaseMigration
{
    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->renameColumn('network_access', 'internet_access');
            $table->renameColumn('telephony_only', 'has_telephony');
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->renameColumn('network_access', 'internet_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->renameColumn('internet_access', 'network_access');
            $table->renameColumn('has_telephony', 'telephony_only');
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->renameColumn('internet_access', 'network_access');
        });
    }
}
