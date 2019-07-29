<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateNetElementAddIdName extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'netelement';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('id_name', 191)->virtualAs("CONCAT(`id`,'_',`name`)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('id_name');
        });
    }
}
