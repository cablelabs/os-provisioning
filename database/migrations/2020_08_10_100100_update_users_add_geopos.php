<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class UpdateUsersAddGeopos extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('geopos_updated_at')->nullable();

            // 6 decimal places corresponds to 10cm accuracy - see e.g. https://cweiske.de/tagebuch/geokoordinaten-kommastellen.htm
            $table->decimal('geopos_x', 9, 6)->nullable();
            $table->decimal('geopos_y', 9, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'geopos_updated_at',
                'geopos_x',
                'geopos_y',
            ]);
        });
    }
}
