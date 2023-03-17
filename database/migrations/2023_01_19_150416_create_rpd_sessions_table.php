<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpdSessionsTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'rpd_session';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $this->up_table_generic($table);
            $table->bigInteger('rpd_id');
            $table->bigInteger('session_id')->nullable();
            $table->integer('address_type')->nullable();
            $table->integer('local_id')->nullable();
            $table->string('internal_id')->nullable()->unique();
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
