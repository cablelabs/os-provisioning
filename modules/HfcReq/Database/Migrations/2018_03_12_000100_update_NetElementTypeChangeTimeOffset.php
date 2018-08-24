<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNetElementTypeChangeTimeOffset extends Migration
{
    // name of the table to create
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->float('pre_conf_time_offset')->nullable()->change();
        });

        DB::statement("UPDATE $this->tablename SET pre_conf_time_offset = pre_conf_time_offset / 1000000");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE $this->tablename SET pre_conf_time_offset = pre_conf_time_offset * 1000000");

        Schema::table($this->tablename, function (Blueprint $table) {
            $table->integer('pre_conf_time_offset')->nullable()->change();
        });
    }
}
