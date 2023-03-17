<?php

use Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNotifMailFromProvBase extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'provbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn($this->tableName, 'notif_mail')) {
            return;
        }

        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('notif_mail');
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
            $table->string('notif_mail')->nullable();
        });
    }
}
