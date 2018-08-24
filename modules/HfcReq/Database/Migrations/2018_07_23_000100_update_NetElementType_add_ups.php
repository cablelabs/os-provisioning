<?php

use Illuminate\Database\Migrations\Migration;

class UpdateNetElementTypeAddUps extends Migration
{
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("REPLACE INTO $this->tablename (id, created_at, updated_at, name, parent_id, pre_conf_oid_id, pre_conf_time_offset, page_reload_time) VALUES (7, NOW(), NOW(), 'UPS', 0, 0, 0, 0)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DELETE FROM $this->tablename WHERE id = 7");
    }
}
