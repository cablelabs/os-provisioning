<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
