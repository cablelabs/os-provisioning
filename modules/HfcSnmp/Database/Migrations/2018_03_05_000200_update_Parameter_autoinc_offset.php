<?php

use Modules\HfcReq\Entities\NetElementType;
use Illuminate\Database\Migrations\Migration;

class UpdateParameterAutoincOffset extends Migration
{
    // name of the table to create
    protected $tablename = 'parameter';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // let netelementtype id start with 1000, so that we can add enough upstream netelementtypes <1000,
        // which won't collide with user defined ones
        DB::statement("UPDATE $this->tablename SET netelementtype_id = netelementtype_id + 1000 where netelementtype_id > 6;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
