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
use Illuminate\Database\Migrations\Migration;

class AddCountryCodeFields extends Migration
{
    protected $tablenames = [
        'contract',
        'modem',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tablenames as $tablename) {
            Schema::table($tablename, function (Blueprint $table) {
                $table->string('country_code', 2)->after('country_id')->nullable()->default(null);
            });
        }

        $global_table = 'global_config';
        Schema::table($global_table, function (Blueprint $table) {
            $table->string('default_country_code', 2)->after('headline2');
        });

        DB::update("UPDATE $global_table SET default_country_code='DE'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tablenames as $tablename) {
            Schema::table($tablename, function (Blueprint $table) {
                $table->dropColumn([
                    'country_code',
                ]);
            });
        }

        $global_table = 'global_config';
        Schema::table($global_table, function (Blueprint $table) {
            $table->dropColumn([
                'default_country_code',
            ]);
        });
    }
}
