<?php

use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddApiTokenToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('api_token', 80)
                ->after('password')
                ->unique()
                ->nullable()
                ->default(null);
        });

        foreach (User::all() as $user) {
            $user->api_token = Str::random(80);
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('api_token');
        });
    }
}
