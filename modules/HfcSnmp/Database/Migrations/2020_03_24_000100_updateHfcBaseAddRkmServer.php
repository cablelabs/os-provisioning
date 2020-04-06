<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHfcBaseAddRkmServer extends Migration
{
    protected $tablename = 'hfcbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('rkm_server')->nullable();          // IP + Port
            $table->string('rkm_server_username')->nullable();
            $table->string('rkm_server_password')->nullable();

            $table->string('video_controller')->nullable();          // IP + Port
            $table->string('video_controller_username')->nullable();
            $table->string('video_controller_password')->nullable();

            $table->string('video_encoder')->nullable();          // IP + Port
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn(['rkm_server', 'video_controller', 'video_encoder', 'rkm_server_username', 'rkm_server_password',
                'video_controller_username', 'video_controller_password',
            ]);
        });
    }
}
