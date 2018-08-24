<?php

use Illuminate\Database\Schema\Blueprint;

class CreateAuthuserTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'authusers';

    // password for inital superuser
    protected $initial_superuser_password = 'toor';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('login_name', 191);
            $table->string('password', 60);
            $table->string('description');
            $table->boolean('active')->default(1);
            $table->rememberToken();

            $table->unique('login_name');
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        // add superuser => needed to configure the monster
        DB::table($this->tablename)->insert([
            'id' => 1,
            'first_name' => 'superuser',
            'last_name' => 'initial',
            'email' => 'root@localhost',
            'login_name' => 'root',
            'password' => \Hash::make($this->initial_superuser_password),
            'description' => 'Superuser to do base config. Initial password is “'.$this->initial_superuser_password.'” – change this ASAP or delete this user!!',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
}
