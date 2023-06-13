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

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;

class UpdateContractAddSmartOntFields extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'contract';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->string('sep_id', 32)->nullable();
            $table->string('oto_id', 64)->nullable();
            $table->smallInteger('oto_port')->unsigned()->nullable();
            $table->string('oto_socket_usage', 64)->nullable();
            $table->string('oto_status', 32)->nullable();
            $table->string('flat_id', 32)->nullable();
            $table->string('alex_status', 32)->nullable();
            $table->string('omdf_id', 128)->nullable();
            $table->string('boc_label', 128)->nullable();
            $table->string('bof_label', 32)->nullable();
            $table->string('type', 32)->nullable()->default('nmsprime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn([
                'sep_id',
                'oto_id',
                'oto_port',
                'oto_socket_usage',
                'oto_status',
                'flat_id',
                'alex_status',
                'omdf_id',
                'boc_label',
                'bof_label',
                'type',
            ]);
        });
    }
}
