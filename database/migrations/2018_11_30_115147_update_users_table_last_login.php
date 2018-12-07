<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class UpdateUsersTableLastLogin extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
        });

        Schema::table('global_config', function (Blueprint $table) {
            $table->unsignedInteger('passwordResetInterval')->default(120);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
            $table->dropColumn('password_changed_at');
        });

        Schema::table('global_config', function (Blueprint $table) {
            $table->dropColumn('passwordResetInterval');
        });
    }
}
