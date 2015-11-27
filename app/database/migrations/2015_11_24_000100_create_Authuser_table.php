<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthuserTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "authusers";

	// password for inital superuser
	protected $initial_superuser_password = "toor";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create($this->tablename, function(Blueprint $table) {

			$this->up_table_generic($table);

			$table->string('first_name');
			$table->string('last_name');
			$table->string('email');
			$table->string('login_name');
			$table->string('password', 60);
			$table->string('description');
			$table->boolean('active')->default(1);
			$table->rememberToken();
		});

		DB::update("INSERT INTO ".$this->tablename." (first_name, last_name, email, login_name, password, description) VALUES('superuser', 'initial', 'root@localhost', 'root', '".Hash::make($this->initial_superuser_password)."', 'Superuser to do base config. Initial password is “".$this->initial_superuser_password."” – change this ASAP or delete this user!!');");

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop($this->tablename);
	}
}
