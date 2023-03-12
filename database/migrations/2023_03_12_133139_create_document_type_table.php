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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tablename = 'documenttype';

    // these are some predefined documenttypes NMSPrime supports out-of-the-box
    protected $init_data = [
        [
            'type' => 'upload',
            'model' => null,
            'module' => null,
            'default_filename_pattern' => null,
            'usable' => 1,
        ],
        [
            'type' => 'letterhead',
            'model' => 'Contract',
            'module' => 'ProvBase',
            'default_filename_pattern' => null,
            'usable' => 1,
        ],
        [
            'type' => 'contract_start',
            'model' => 'Contract',
            'module' => 'ProvBase',
            'default_filename_pattern' => '[contract_start]__{contract.number}.pdf',
            'usable' => 1,
        ],
        [
            'type' => 'contract_change',
            'model' => 'Contract',
            'module' => 'ProvBase',
            'default_filename_pattern' => '[contract_change]__{contract.number}.pdf',
            'usable' => 1,
        ],
        [
            'type' => 'contract_end',
            'model' => 'Contract',
            'module' => 'ProvBase',
            'default_filename_pattern' => '[contract_end]__{contract.number}.pdf',
            'usable' => 1,
        ],
        [
            'type' => 'connection_info',
            'model' => 'Contract',
            'module' => 'ProvBase',
            'default_filename_pattern' => '[connection_info]__{contract.number}.pdf',
            'usable' => 1,
        ],
        [
            'type' => 'phonenumber_activation',
            'model' => 'PhonenumberManagement',
            'module' => 'ProvVoip',
            'default_filename_pattern' => '[phonenumber_activation]__{phonenumber.country_code}_{phonenumber.prefix_number}_{phonenumber.number}.pdf',
            'usable' => 1,
        ],
        [
            'type' => 'phonenumber_deactivation',
            'model' => 'PhonenumberManagement',
            'module' => 'ProvVoip',
            'default_filename_pattern' => '[phonenumber_deactivation]__{phonenumber.country_code}_{phonenumber.prefix_number}_{phonenumber.number}.pdf',
            'usable' => 1,
        ],
        [
            'type' => 'invoice',
            'model' => 'Invoice',
            'module' => 'BillingBase',
            'default_filename_pattern' => '[invoice]__{invoice.year}_{invoice.month}__{invoice.invoice_number}.pdf',
            'usable' => 0,  // deeply embedded in Invoice – will be used later
        ],
        [
            'type' => 'cdr',
            'model' => 'Invoice',
            'module' => 'BillingBase',
            'default_filename_pattern' => '[cdr]__{invoice.year}_{invoice.month}.pdf',
            'usable' => 0   // deeply embedded in Invoice – will be used later,
        ],
    ];

    /**
     * Run the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('type');
            $table->string('type_view')->nullable()->default(null);                 // document type as shown in view; can be set through artisan command (needed for searchable datatable entries)
            $table->string('module')->nullable()->default(null);                    // a model this type belongs to (used to show/hide certain types depending on active modules)
            $table->string('model')->nullable()->default(null);                     // the model a document of this type can be created at
            $table->string('default_filename_pattern')->nullable()->default(null);  // used to generate filename; overwritten in templates; strings in [] will be replaced by value in type_view
            $table->boolean('usable');
        });

        foreach ($this->init_data as $entry) {
            $entry['created_at'] = $entry['updated_at'] = date('Y-m-d H:i:s');
            DB::table($this->tablename)->insert($entry);
        }

        Artisan::call('nms:translateDatabase', ['lang' => 'en', 'table' => 'documenttype']);
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
        Schema::drop($this->tablename);
    }
};
