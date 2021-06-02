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

class CreatePhoneTariffTable extends BaseMigration
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
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('external_identifier');		// at envia TEL this is a integer or a string…
            $table->string('name');						// name to show in forms
            $table->enum('type', ['purchase', 'sale']);
            $table->string('description');
            $table->boolean('usable')->default(1);		// there are more envia TEL variations as we really use (e.g. MGCP stuff) – can be used for temporary deactivation of tariffs or to prevent a tariff from being assingned again
        });

        $this->set_fim_fields([
            'external_identifier',
            'name',
            'description',
        ]);

        return parent::up();
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
        Schema::drop($this->tablename);
    }
}
