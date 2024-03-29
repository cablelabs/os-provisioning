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

class updateGlobalConfigAddLoginBackgroundImages extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Note: columns can't be named equally as select fields appear on same site and with our generic and overloading js it wouldn't work
        Schema::table('global_config', function (Blueprint $table) {
            $table->string('login_img')->nullable();
        });

        Schema::table('ccc', function (Blueprint $table) {
            $table->string('bgimg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_config', function (Blueprint $table) {
            $table->dropColumn('login_img');
        });

        Schema::table('ccc', function (Blueprint $table) {
            $table->dropColumn('bgimg');
        });
    }
}
