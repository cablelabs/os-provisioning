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
use Illuminate\Support\Facades\Schema;

class UpdateCcapAddSummaryCols extends BaseMigration
{
    public $migrationScope = 'database';

    protected $table = 'ccap';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->integer('cms')->nullable();
            $table->integer('mtas')->nullable();
            $table->integer('dsgs')->nullable();
            $table->integer('rpds')->nullable();
            $table->float('dpa1_links_overutilized')->nullable();       // in %
            $table->float('dpa2_links_overutilized')->nullable();       // in %
            $table->integer('redundancy')->nullable();

            $table->integer('uptime')->nullable();
            $table->string('sw_ver')->nullable();
            $table->string('config_compliance')->nullable();
            $table->string('serial')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn([
                'cms',
                'mtas',
                'dsgs',
                'rpds',
                'dpa1_links_overutilized',
                'dpa2_links_overutilized',
                'redundancy',
                'uptime',
                'sw_ver',
                'config_compliance',
                'serial',
            ]);
        });
    }
}
