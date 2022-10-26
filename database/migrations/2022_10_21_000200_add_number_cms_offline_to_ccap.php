<?php

use Database\Migrations\BaseMigration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddNumberCmsOfflineToCcap extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tableName = 'ccap';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->integer('cms_offline')->nullable();
            $table->integer('mtas_offline')->nullable();
            $table->integer('rpds_offline')->nullable();
            $table->integer('stbs_offline')->nullable();
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
            $table->dropColumn([
                'cms_offline',
                'mtas_offline',
                'rpds_offline',
                'stbs_offline',
            ]);
        });
    }
}
