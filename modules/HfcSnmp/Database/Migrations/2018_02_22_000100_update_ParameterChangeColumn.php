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
 * Make third_dimension Column not nullable for better use in SnmpController
 */
class UpdateParameterChangeColumn extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'parameter';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->boolean('third_dimension')->nullable(false)->default(0)->change();
            $table->boolean('diff_param')->nullable(false)->default(0)->change();
        });

        DB::table($this->tablename)->whereNull('third_dimension')->update(['third_dimension' => 0]);
        DB::table($this->tablename)->whereNull('diff_param')->update(['third_dimension' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Dont revert as this assumption is already needed but missing
    }
}
