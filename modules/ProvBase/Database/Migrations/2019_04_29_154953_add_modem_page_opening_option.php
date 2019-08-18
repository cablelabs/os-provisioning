<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddModemPageOpeningOption extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provbase', function (Blueprint $table) {
            $table->boolean('modem_edit_page_new_tab');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provbase', function (Blueprint $table) {
            $table->dropColumn('modem_edit_page_new_tab');
        });
    }
}
