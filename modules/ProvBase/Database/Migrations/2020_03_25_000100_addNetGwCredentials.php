<?php

use Illuminate\Database\Schema\Blueprint;

class AddNetGwCredentials extends BaseMigration
{
    protected $tablename = 'netgw';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('ssh_auto_prov')->default(0);
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
            $table->dropColumn(['username', 'password', ['ssh_auto_prov']]);
        });
    }
}
