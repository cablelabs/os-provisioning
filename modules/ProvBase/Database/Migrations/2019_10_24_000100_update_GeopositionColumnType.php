<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateGeopositionColumnType extends BaseMigration
{
    /**
     * Run the migrations. Laravel always updates modems with coordinates where float type leads to rounding errors
     * We can avoid this and the erronous mentioned changes in the GuiLog entries by using the decimal type
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->decimal('x', 11, 8)->nullable()->change();
            $table->decimal('y', 11, 8)->nullable()->change();
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->decimal('x', 11, 8)->nullable()->change();
            $table->decimal('y', 11, 8)->nullable()->change();
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
            $table->float('x')->nullable()->change();
            $table->float('y')->nullable()->change();
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->float('x')->nullable()->change();
            $table->float('y')->nullable()->change();
        });
    }
}
