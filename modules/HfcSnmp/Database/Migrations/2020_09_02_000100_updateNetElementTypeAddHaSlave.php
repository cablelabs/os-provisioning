<?php

use Modules\HfcReq\Entities\NetElementType;
use Illuminate\Database\Migrations\Migration;

class UpdateNetElementTypeAddHaSlave extends Migration
{
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @author Patrick Reichel, adapted from Nino
     * @return void
     */
    public function up()
    {
        // Make sure that netelementtype ID 10 is free
        $id = 9;
        do {
            $id++;

            $exists = NetElementType::find($id);
        } while ($exists);

        if ($id != 10) {
            DB::statement("UPDATE $this->tablename SET id=$id WHERE $id=10");
            DB::statement("UPDATE netelement SET netelementtype_id=$id WHERE netelementtype_id=10");
        }

        DB::statement("INSERT INTO $this->tablename (id, created_at, updated_at, name) VALUES (10, NOW(), NOW(), 'NMSPrime ProvHA slave')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM $this->tablename WHERE id=10");
    }
}
