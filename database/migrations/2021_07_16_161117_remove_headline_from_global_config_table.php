<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RemoveHeadlineFromGlobalConfigTable extends BaseMigration
{
    public $tableName = 'global_config';
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('headline1');
            $table->renameColumn('headline2', 'headline');
            $table->boolean('isAllNetsSidebarEnabled')->default(false);
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
            $table->string('headline1');
            $table->renameColumn('headline', 'headline2');
            $table->dropColumn('isAllNetsSidebarEnabled');
        });
    }
}
