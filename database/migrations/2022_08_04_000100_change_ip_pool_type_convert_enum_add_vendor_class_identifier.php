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

class ChangeIpPoolTypeConvertEnumAddVendorClassIdentifier extends BaseMigration
{
    public $migrationScope = 'database';
    protected $table = 'ippool';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE {$this->table} ALTER COLUMN type TYPE VARCHAR(191)");
        DB::statement('DROP TYPE ippool_type');

        Schema::table($this->table, function (Blueprint $table) {
            $table->string('vendor_class_identifier')->nullable();
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
            $table->dropColumn('vendor_class_identifier');
        });

        DB::statement("CREATE TYPE ippool_type AS ENUM ('CM', 'CPEPub', 'CPEPriv', 'MTA')");
        DB::statement("ALTER TABLE {$this->table} ALTER COLUMN type TYPE ippool_type USING type::ippool_type");
    }
}
