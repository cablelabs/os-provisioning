<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldSupportStateToCmtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmts', function (Blueprint $table) {
            $table->enum('support_state', ['verifying', 'full-support', 'not-supported', 'restricted'])->default('verifying');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmts', function (Blueprint $table) {
            $table->dropColumn('support_state');
        });
    }
}
