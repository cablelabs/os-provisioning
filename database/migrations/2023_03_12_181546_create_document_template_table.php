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
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tablename = 'documenttemplate';

    // have to correlate with order in document type migration
    protected $doctypes = [
        // 1 is special type upload ⇒ no document template needed/used
        2 => 'letterhead',
        3 => 'contract_start',
        4 => 'contract_change',
        5 => 'contract_end',
        6 => 'connection_info',
        7 => 'phonenumber_activation',
        8 => 'phonenumber_deactivation',
        9 => 'invoice',
        10 => 'cdr',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('documenttype_id')->unsigned();                             // 1 is letterhead
            $table->string('file');                                                     // the path the template file can be found at
            $table->string('format')->nullable()->default(null);                        // e.g. LaTeX
            $table->integer('company_id')->unsigned()->nullable()->default(null);       // if null: use template from global config
            $table->integer('sepaaccount_id')->unsigned()->nullable()->default(null);   // if null: use template from company
            $table->string('filename_pattern')->nullable()->default(null);              // used to generate filename (overwrites DocumentType if given)
          });

        foreach ($this->doctypes as $id => $doctype) {
            $entry['created_at'] = $entry['updated_at'] = date('Y-m-d H:i:s');
            $entry['documenttype_id'] = $id;
            $entry['format'] = 'LaTeX';
            $entry['file'] = 'default_'.$doctype.'.tex';
            $entry['sepaaccount_id'] = null;
            $entry['company_id'] = null;
            $entry['filename_pattern'] = null;
            DB::table($this->tablename)->insert($entry);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
};
