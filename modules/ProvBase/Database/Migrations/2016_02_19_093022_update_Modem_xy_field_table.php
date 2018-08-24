<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateModemXyFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modem', function (Blueprint $table) {
            // NOTE: a normal float has only 2 precisions
            // NOTE: double does not work with alter table in Laravel 5.1.29. Ask google.
            //      - float(.., 8)->default(0) forces SQL to use a double field
            //      - without ->default(0) we will also get a double of size (8,2). Funny
            $table->float('x', 8)->default(0)->change();
            $table->float('y', 8)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modem', function (Blueprint $table) {
            $table->float('x')->change();
            $table->float('y')->change();
        });
    }
}
