<?php

use Illuminate\Database\Schema\Blueprint;

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
class AddSqliteTablesForJobs extends BaseMigration
{
    public $migrationScope = 'system';

    protected $connection = 'sqlite-jobs';

    /**
     * Run the migrations.
     * To be able to queue jobs at HA slave instances (which have no write access to database) we
     * store this informations within small sqlite databases.
     *
     * @return void
     */
    public function up()
    {
        $dbFile = config("database.connections.$this->connection.database");
        $dbDir = dirname($dbFile);

        touch($dbFile);
        chown($dbFile, 'apache');
        chown($dbDir, 'apache');
        chgrp($dbFile, 'apache');
        chmod($dbFile, 0640);
        chmod($dbDir, 0755);

        Schema::connection($this->connection)->create('jobs', function (Blueprint $table) {
            $table->integer('id');
            $table->string('queue');
            $table->longText('payload');
            $table->tinyInteger('attempts')->unsigned();
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
            $table->index(['queue', 'reserved_at']);
            $table->primary('id');
        });

        Schema::connection($this->connection)->create('failed_jobs', function (Blueprint $table) {
            $table->integer('id');
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->timestamp('failed_at')->useCurrent();
            $table->longText('exception');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        unlink(config("database.connections.$this->connection.database"));
    }
}
