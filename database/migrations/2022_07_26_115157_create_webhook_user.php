<?php

class CreateWebhookUser extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('users')->insert(['first_name' => 'web', 'last_name' => 'hook', 'email' => 'someone@example.com', 'password' => Hash::make('Secretroot123')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('users')->where('email', 'someone@example.com')->delete();
    }
}
