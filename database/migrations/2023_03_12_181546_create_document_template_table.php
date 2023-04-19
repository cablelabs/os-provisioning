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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\DocumentManagement\DocumentTypes\DocumentType;
use Modules\DocumentManagement\DocumentTypes\InformationalLetterDocumentType;

return new class extends BaseMigration
{
    public $migrationScope = 'database';
    protected $tablename = 'documenttemplate';

    protected $defaultSerialLetters = [
        ['name' => 'Serial letter 1', 'file' => 'default_serial_letter-1.tex'],
        ['name' => 'Serial letter 2', 'file' => 'default_serial_letter-2.tex'],
        ['name' => 'Serial letter 3', 'file' => 'default_serial_letter-3.tex'],
        ['name' => 'Serial letter 4', 'file' => 'default_serial_letter-4.tex'],
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
            $table->string('name')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('document_type');
            $table->string('type_view');
            $table->string('file');                                                     // the path the template file can be found at
            $table->string('format')->nullable()->default(null);                        // e.g. LaTeX
            $table->string('filename_pattern')->nullable()->default(null);              // used to generate filename (overwrites DocumentType if given)
            $table->boolean('is_default')->default(false);
        });

        $timestamps = date('Y-m-d H:i:s');
        foreach (DocumentType::getTypes() as $typeClass => $name) {
            $entry['created_at'] = $entry['updated_at'] = $timestamps;
            $entry['document_type'] = $typeClass;
            $entry['type_view'] = $name;
            $entry['name'] = $name;
            $entry['format'] = 'LaTeX';
            $entry['file'] = $typeClass::getDefaultTemplatePath();
            $entry['filename_pattern'] = $typeClass::getDefaultFilenamePattern();
            $entry['is_default'] = true;
            DB::table($this->tablename)->insert($entry);
        }

        foreach ($this->defaultSerialLetters as $serialLetter) {
            DB::table($this->tablename)->insert([
                'name' => $serialLetter['name'],
                'file' => $serialLetter['file'],
                'is_default' => true,
                'format' => 'LaTeX',
                'document_type' => InformationalLetterDocumentType::class,
                'type_view' => InformationalLetterDocumentType::getTranslatedName(),
                'filename_pattern' => InformationalLetterDocumentType::getDefaultFilenamePattern(),
                'created_at' => $timestamps,
                'updated_at' => $timestamps,
            ]);
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
