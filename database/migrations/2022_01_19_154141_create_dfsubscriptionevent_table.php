<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDfSubscriptionEventTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'dfsubscriptionevent';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->text('description')->nullable();
            $table->string('status', 32)->nullable(false);
            $table->string('timestamp', 32)->nullable(false);
            $table->integer('dfsubscription_id')->unsigned();
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
