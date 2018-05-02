<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAuthroleToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authrole', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::rename('authrole', 'roles');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->enum('type', array('role', 'client'))->default('role');
        });

        Schema::rename('roles', 'authrole');
    }
}
