<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SetIndexForNetelementsInModemTable extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modem', function (Blueprint $table) {
            $table->index('netelement_id');
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
            $table->dropIndex(['netelement_id']);
        });
    }
}
