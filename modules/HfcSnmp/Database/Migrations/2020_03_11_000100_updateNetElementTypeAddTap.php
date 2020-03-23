<?php

use Illuminate\Database\Migrations\Migration;
use \Modules\HfcReq\Entities\NetElementType;

class UpdateNetElementTypeAddTap extends Migration
{
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make sure that netelementtype IDs 8 & 9 are free
        $id = 7;
        do {
            $id++;

            $exists = NetElementType::whereIn('id', [$id, $id+1])->first();
        } while ($exists);

        if ($id != 8) {
            DB::statement("UPDATE $this->tablename SET id=$id WHERE $id=8");
            DB::statement("UPDATE netelement SET netelementtype_id=$id WHERE netelementtype_id=8");

            $id++;
            DB::statement("UPDATE $this->tablename SET id=$id WHERE $id=9");
            DB::statement("UPDATE netelement SET netelementtype_id=$id WHERE netelementtype_id=9");
        }

        DB::statement("INSERT INTO $this->tablename (id, created_at, updated_at, name) VALUES (8, NOW(), NOW(), 'Tap')");
        DB::statement("INSERT INTO $this->tablename (id, created_at, updated_at, name, parent_id) VALUES (9, NOW(), NOW(), 'Tap-Port', 8)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM $this->tablename WHERE id in (8, 9)");
    }
}
