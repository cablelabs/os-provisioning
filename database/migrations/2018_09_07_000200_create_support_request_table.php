<?php

use Illuminate\Database\Schema\Blueprint;

class CreateSupportRequestTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'supportrequest';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);
            $table->string('category');
            $table->string('priority');
            $table->string('mail')->nullable();
            $table->string('phone')->nullable();
            $table->string('text')->nullable();
            $table->string('sla_name')->nullable();
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
