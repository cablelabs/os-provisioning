<?php

use Illuminate\Support\Facades\DB;
use Database\Migrations\BaseMigration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ChangeIpTypeToInetNetelementTable extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'netelement';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            DB::statement("ALTER TABLE $this->tableName ALTER COLUMN ip TYPE inet USING ip::inet");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            DB::statement("ALTER TABLE $this->tableName ALTER COLUMN ip TYPE varchar(191)");
        });
    }
}
