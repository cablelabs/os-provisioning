<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractUniquenumberfield extends Migration
{
    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->unique('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->dropUnique(['number']);
        });
    }
}
