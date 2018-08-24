<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Remove unique index on login_name column so that we can use soft deletes for Users as well
 *
 * @author Nino Ryschawy
 */
class UpdateAuthusersUndoUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authusers', function (Blueprint $table) {
            $table->dropUnique('authusers_login_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authusers', function (Blueprint $table) {
            $table->unique('login_name');
        });
    }
}
