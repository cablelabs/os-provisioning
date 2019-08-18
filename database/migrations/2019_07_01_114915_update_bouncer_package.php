<?php

use App\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBouncerPackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->integer('entity_id')->unsigned()->nullable()->change();
            $table->string('entity_type', 150)->nullable()->change();
        });

        DB::table('permissions')
        ->where(['entity_type' => Role::class])
        ->update(['entity_type' => (new Role)->getMorphClass()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->integer('entity_id')->unsigned()->nullable(false)->change();
            $table->string('entity_type', 150)->nullable(false)->change();
        });
    }
}
