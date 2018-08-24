<?php

use Illuminate\Database\Schema\Blueprint;

class CreateAuthusermetaTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'authusermeta';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('user_id')->unsigned();
            $table->integer('meta_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('authuser');
            $table->foreign('meta_id')->references('id')->on('authmeta');

            $table->unique(['user_id', 'meta_id']);
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        DB::update('INSERT INTO '.$this->tablename.' (user_id, meta_id) VALUES(1, 1);');
        DB::update('INSERT INTO '.$this->tablename.' (user_id, meta_id) VALUES(1, 2);');
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
