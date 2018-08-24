<?php

use Modules\HfcReq\Entities\NetElementType;
use Illuminate\Database\Migrations\Migration;

class UpdateNetElementTypeAutoincOffset extends Migration
{
    // name of the table to create
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // let netelementtype id start with 1000, so that we can add enough upstream netelementtypes <1000,
        // which won't collide with user defined ones
        DB::statement("ALTER TABLE $this->tablename AUTO_INCREMENT = 1000;");
        DB::statement("UPDATE $this->tablename SET id = id + 1000 where id > 6;");
        DB::statement("UPDATE $this->tablename SET parent_id = parent_id + 1000 where id > 6 and parent_id > 6;");
        DB::statement('UPDATE netelement SET netelementtype_id = netelementtype_id + 1000 where netelementtype_id > 6;');
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
