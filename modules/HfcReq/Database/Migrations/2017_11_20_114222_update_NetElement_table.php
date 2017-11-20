<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNetElementTable extends Migration {

    // name of the table to create
    protected $tablename = "netelement";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function(Blueprint $table)
        {
            $table->integer('cmts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function(Blueprint $table)
        {
            $table->dropColumn(['cmts']);
        });
    }

}
