<?php

use Illuminate\Database\Schema\Blueprint;

class updateEndpointAddV6 extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'endpoint';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('version')->sizeof(10)->nullable();
            $table->string('prefix')->nullable();   // can be multiple comma separated addresses
        });

        \Modules\ProvBase\Entities\Endpoint::withTrashed()->where('id', '!=', 0)->update(['version' =>  4]);
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
            ]);
        });
    }
}
