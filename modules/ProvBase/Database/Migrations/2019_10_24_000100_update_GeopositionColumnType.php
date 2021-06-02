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

class UpdateGeopositionColumnType extends BaseMigration
{
    /**
     * Run the migrations. Laravel always updates modems with coordinates where float type leads to rounding errors
     * We can avoid this and the erronous mentioned changes in the GuiLog entries by using the decimal type
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->decimal('x', 11, 8)->nullable()->change();
            $table->decimal('y', 11, 8)->nullable()->change();
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->decimal('x', 11, 8)->nullable()->change();
            $table->decimal('y', 11, 8)->nullable()->change();
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
            $table->float('x')->nullable()->change();
            $table->float('y')->nullable()->change();
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->float('x')->nullable()->change();
            $table->float('y')->nullable()->change();
        });
    }
}
