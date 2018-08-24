<?php

use Illuminate\Database\Schema\Blueprint;

class CreatePhonenumberTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonenumber';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('mta_id')->unsigned()->default(1);
            $table->tinyInteger('port')->unsigned();
            $table->enum('country_code', ['0049']);
            $table->string('prefix_number');
            $table->string('number');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('sipdomain')->nullable();
            $table->boolean('active');
            $table->boolean('is_dummy')->default(0);
        });

        $this->set_fim_fields(['prefix_number', 'number', 'username']);
        $this->set_auto_increment(300000);

        // insert dummy number
        DB::update('INSERT INTO '.$this->tablename." (prefix_number,number,active,is_dummy,deleted_at) VALUES('0000','00000',1,1,NOW());");

        return parent::up();
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
