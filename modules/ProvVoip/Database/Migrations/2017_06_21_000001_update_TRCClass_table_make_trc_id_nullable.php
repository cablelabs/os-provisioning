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

/**
 * Updater to add col for technical method of purchase tariffs
 *
 * @author Patrick Reichel
 */
class UpdateTRCClassTableMakeTrcIdNullable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'trcclass';

    /**
     * Run the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function up()
    {

        // make trc_id nullable ⇒ this will be used for unset TRCClass
        DB::statement('ALTER TABLE `'.$this->tablename.'` MODIFY `trc_id` INTEGER UNSIGNED UNIQUE NULL;');

        // insert value for not set TRC class (this e.g. will be used in autogenerated phonenumbermanagements
        DB::update('INSERT INTO `'.$this->tablename."` (trc_id, trc_short, trc_description) VALUES(NULL, 'n/a', 'unknown or not set');");
    }

    /**
     * Reverse the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function down()
    {

        // remove the null entry
        DB::statement('DELETE FROM `'.$this->tablename.'` WHERE `trc_id` IS NULL');

        // make trc_id not nullable
        DB::statement('ALTER TABLE '.$this->tablename.' MODIFY `trc_id` INTEGER UNSIGNED UNIQUE NOT NULL;');
    }
}
