<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateArpTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'arp';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $this->up_table_generic($table);
            $table->string('display_key')->nullable();
            $table->string('address')->nullable();
            $table->string('type')->nullable();
            $table->string('interface')->nullable();
            $table->string('mac')->nullable();
            $table->integer('netelement_id');
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
