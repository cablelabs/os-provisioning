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
 * Updater to add col for technical method of purchase tariffs
 *
 * @author Patrick Reichel
 */
class UpdatePhoneTariffTableAddMethodCol extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonetariff';

    /**
     * Run the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->enum('voip_protocol', ['MGCP', 'SIP'])->nullable();
        });

        $this->set_fim_fields([
            'external_identifier',
            'name',
            'description',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('voip_protocol');
        });

        $this->set_fim_fields([
            'external_identifier',
            'name',
            'description',
        ]);
    }
}
