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

return new class extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'statistics_query';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $this->up_table_generic($table);
            $table->string('name');
            $table->string('customer_type')->nullable();
            $table->string('product_group')->nullable();
            $table->json('tariffs')->nullable();
            $table->string('status')->nullable();
            $table->string('antenna_communities')->nullable();
            $table->json('zip_code_filter')->nullable();
            $table->string('gender')->nullable();
            $table->string('age_groups')->nullable();
            $table->boolean('split_combination_packages')->nullable();
            $table->boolean('consider_tariff_change');
            $table->boolean('revenue');
            $table->boolean('diagram');
            $table->boolean('upsell');
            $table->date('from');
            $table->date('to');
            $table->string('auto')->nullable(); // will contain the cron string to automatically retrieve statistic repetitive
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
};
