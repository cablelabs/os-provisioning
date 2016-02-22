<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Controllers\AuthuserController;

class AlterAuthuserTable extends Migration
{
    protected $tablename = "authusers";

    protected $supported_languages = ['en', 'browser', 'de'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function ($table) {
            $table->enum('language', $this->supported_languages)->default('en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function ($table) {
            $table->dropColumn('language');
        });
    }
}