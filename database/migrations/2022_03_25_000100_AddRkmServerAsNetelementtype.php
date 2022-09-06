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
use Modules\HfcReq\Entities\NetElementType;

class AddRkmServerAsNetelementtype extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT into netelementtype (id, created_at, updated_at, name, vendor, base_type_id) values (15, current_timestamp(0), current_timestamp(0), 'RKM-Server', 'SAT-Kabel', 15)");

        Schema::table('netelement', function (Blueprint $table) {
            $table->string('username')->nullable();
            $table->string('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        NetElementType::where('id', 15)->forceDelete();

        Schema::table('netelement', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }
}
