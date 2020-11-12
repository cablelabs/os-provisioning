<?php

use Illuminate\Database\Schema\Blueprint;

class updateIpPoolAddV6 extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'ippool';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('version', 1)->default('4');
            $table->string('prefix')->nullable();
            $table->string('prefix_len')->sizeof(3)->nullable();
            $table->string('delegated_len')->sizeof(3)->nullable();
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
            $table->dropColumn([
                'version',
                'prefix',
                'prefix_len',
                'delegated_len',
            ]);
        });
    }
}
