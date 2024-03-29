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

class CreateDfsubscriptionTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'dfsubscription';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $this->upTableGeneric($table);

            $table->string('service_name')->nullable();
            $table->string('service_id')->nullable();

            $table->string('contact_no')->nullable();
            $table->string('contact_first_name')->nullable();
            $table->string('contact_last_name')->nullable();
            $table->string('contact_company_name')->nullable();
            $table->string('contact_street')->nullable();
            $table->string('contact_street_no', 16)->nullable();
            $table->string('contact_postal_code', 16)->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_country')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('contact_notes')->nullable();

            $table->integer('subscription_id')->unsigned()->nullable();
            $table->integer('subscription_end_point_id')->unsigned()->nullable();
            $table->string('sf_sla', 16)->nullable();
            $table->string('status', 32)->nullable();
            $table->string('wishdate', 32)->nullable();
            $table->string('switchdate', 32)->nullable();
            $table->string('modificationdate', 32)->nullable();

            $table->string('l1_handover_equipment_name', 128)->nullable();
            $table->string('l1_handover_equipment_rack', 64)->nullable();
            $table->string('l1_handover_equipment_slot', 64)->nullable();
            $table->string('l1_handover_equipment_port', 16)->nullable();

            $table->string('l1_breakout_cable', 128)->nullable();
            $table->string('l1_breakout_fiber', 16)->nullable();

            $table->string('alau_order_ref')->nullable();
            $table->text('note')->nullable();

            $table->integer('contract_id')->unsigned();
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
}
