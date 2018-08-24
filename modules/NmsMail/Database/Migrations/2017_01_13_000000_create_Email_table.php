<?php

use Illuminate\Database\Schema\Blueprint;

class CreateEmailTable extends BaseMigration
{
    protected $tablename = 'email';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('contract_id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->string('localpart', 64);
            $table->string('password');
            $table->integer('index')->unsigned();
            $table->boolean('greylisting');
            $table->boolean('blacklisting');
            $table->string('forwardto');
        });
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
}
