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
use Modules\ProvVoip\Console\TRCClassDatabaseUpdaterCommand;

class CreateTRCClassMovedToProvVoipTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'trcclass';

    /**
     * Run the migrations.
     *
     * ATTENTION: TRCClass has been moved from ProvVoipEnvia (needed on PhonenumberManagement in each case!)
     *
     * @return void
     */
    public function up()
    {
        // as there could exist a table created on the old ProvVoipEnvia migration we have to check for this special case
        // do nothing in this case!
        if (! Schema::hasTable($this->tablename)) {
            Schema::create($this->tablename, function (Blueprint $table) {
                $this->up_table_generic($table);

                $table->integer('trc_id')->unsigned()->unique();
                $table->string('trc_short');
                $table->string('trc_description');
            });

            // insert a dummy entry to prevent exceptions in initial environments
            // this will be overwritten/deleted in updating process
            DB::update('INSERT INTO '.$this->tablename." (trc_id, trc_short, trc_description) VALUES(0, 'n/a', 'Dummy entry – no TRC classes known.');");

            // empty csv hash (if exists; to be sure that newly created table will be filled)
            $updater = new TRCClassDatabaseUpdaterCommand();
            $updater->clear_hash_file();

            // to fill this table call “php artisan provvoip:update_trc_class_database“

            return parent::up();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // this table can be deleted from rolling back ProvVoipEnvia – so we have to check for existance at this point
        Schema::dropIfExists($this->tablename);
    }
}
