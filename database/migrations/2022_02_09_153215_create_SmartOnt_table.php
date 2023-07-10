<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartOntTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'smartont';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $this->upTableGeneric($table);

            $table->string('default_service_name')->nullable();
            $table->string('default_service_id')->nullable();
            $table->string('default_contact_first_name')->nullable();
            $table->string('default_contact_last_name')->nullable();
            $table->string('default_contact_company')->nullable();
            $table->string('default_contact_phone')->nullable();
        });

        // Attention: while “VALUES ("n/a", "n/a")” worked on mariadb it crashes at postgres
        DB::update('INSERT INTO '.$this->tableName."(default_service_name, default_service_id) VALUES ('n/a', 'n/a');");
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
