<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractBirthdayNullable extends Migration
{
    // name of the table to change
    protected $tablename = 'contract';

    /**
     * Run the migrations.
     * Because of new validation rules birthday can now be NULL.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->date('birthday')->nullable()->change();
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
            $table->date('birthday')->nullable(false)->change();
        });
    }
}
