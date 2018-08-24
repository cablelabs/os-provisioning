<?php

use Illuminate\Database\Schema\Blueprint;

class CreateDomainTable extends BaseMigration
{
    protected $tablename = 'domain';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name');
            $table->string('alias');
            $table->enum('type', ['SIP', 'Email', 'DHCP']);
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
