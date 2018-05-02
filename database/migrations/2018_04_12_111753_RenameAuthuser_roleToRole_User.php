<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAuthuserRoleToRoleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('authuser_role', 'role_user');

        Schema::table('role_user', function (Blueprint $table) {
            if (Schema::hasColumn('role_user', 'id')) {
                $table->dropColumn('id');
            }

            $table->foreign('role_id')
            ->references('id')
            ->on('roles')
            ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->primary(['role_id', 'user_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['user_id']);
            $table->dropPrimary();
        });

        Schema::rename('role_user', 'authuser_role');
    }
}
