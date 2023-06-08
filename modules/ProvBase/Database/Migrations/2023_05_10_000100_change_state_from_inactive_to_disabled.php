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

class ChangeStateFromInactiveToDisabled extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update modem set ont_state='disabled' where ont_state = 'inactive'");
        DB::update("update modem set next_ont_state='disabled' where next_ont_state like 'inactive'");
        DB::update("update endpoint set state='disabled' where state='inactive'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::update("update modem set ont_state='inactive' where ont_state = 'disabled'");
        DB::update("update modem set next_ont_state='inactive' where next_ont_state like 'disabled'");
        DB::update("update endpoint set state='inactive' where state='disabled'");
    }
}
