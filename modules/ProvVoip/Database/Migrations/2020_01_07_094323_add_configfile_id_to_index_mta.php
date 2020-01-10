<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddConfigfileIdToIndexMta extends \BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mta', function (Blueprint $table) {
            $table->index('configfile_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mta', function (Blueprint $table) {
            $table->dropIndex(['configfile_id']);
        });
    }
}
