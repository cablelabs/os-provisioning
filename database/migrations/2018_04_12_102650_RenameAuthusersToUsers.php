<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAuthusersToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('authusers', 'users');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('users', 'authusers');

        /* Schema::table('authusers', function (Blueprint $table) {
            if (Schema::hasColumn('authusers', 'remember_token')) {
                $table->drop('remember_token');
            }
        }); */
    }
}
