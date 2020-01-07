<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfigfileIdToIndex extends \BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modem', function (Blueprint $table) {
            $table->index('configfile_id');
        });

        Schema::table('mta', function (Blueprint $table) {
            $table->index('configfile_id');
        });

        Schema::table('configfile', function (Blueprint $table) {
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modem', function (Blueprint $table) {
            $table->dropIndex(['configfile_id']);
        });

        Schema::table('mta', function (Blueprint $table) {
            $table->dropIndex(['configfile_id']);
        });

        Schema::table('configfile', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
        });
    }
}
