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

/**
 * Rename Table and Remove unique index on name column so that we can use soft deletes for Authroles
 *
 * @author Nino Ryschawy
 */
class RenameAuthmetaToAuthrole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authmetas', function (Blueprint $table) {
            // $table->dropUnique('authmetas_name_type_unique');
        });

        Schema::rename('authmetas', 'authrole');

        Schema::table('authusermeta', function (Blueprint $table) {
            $table->renameColumn('meta_id', 'role_id');
        });
        Schema::rename('authusermeta', 'authuser_role');

        Schema::table('authmetacore', function (Blueprint $table) {
            $table->renameColumn('meta_id', 'role_id');
        });
        Schema::rename('authmetacore', 'authrole_core');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('authrole', 'authmetas');

        Schema::table('authmetas', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::rename('authuser_role', 'authusermeta');
        Schema::table('authusermeta', function (Blueprint $table) {
            $table->renameColumn('role_id', 'meta_id');
        });
        Schema::rename('authrole_core', 'authmetacore');
        Schema::table('authmetacore', function (Blueprint $table) {
            $table->renameColumn('role_id', 'meta_id');
        });
    }
}
