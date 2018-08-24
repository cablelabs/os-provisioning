<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryCodeFields extends Migration
{
    protected $tablenames = [
        'contract',
        'modem',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tablenames as $tablename) {
            Schema::table($tablename, function (Blueprint $table) {
                $table->string('country_code', 2)->after('country_id')->nullable()->default(null);
            });
        }

        $global_table = 'global_config';
        Schema::table($global_table, function (Blueprint $table) {
            $table->string('default_country_code', 2)->after('headline2');
        });

        DB::update("UPDATE $global_table SET default_country_code='DE'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tablenames as $tablename) {
            Schema::table($tablename, function (Blueprint $table) {
                $table->dropColumn([
                    'country_code',
                ]);
            });
        }

        $global_table = 'global_config';
        Schema::table($global_table, function (Blueprint $table) {
            $table->dropColumn([
                    'default_country_code',
                ]);
        });
    }
}
