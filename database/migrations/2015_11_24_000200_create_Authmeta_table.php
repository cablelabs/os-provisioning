<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthmetaTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "authmeta";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create($this->tablename, function(Blueprint $table) {

			$this->up_table_generic($table);

			$table->string('name');
			$table->enum('type', array('role', 'client'));
			$table->string('description');
		});

		DB::update("INSERT INTO ".$this->tablename." (name, type, description) VALUES('super_admin', 'role', 'Is allowed to do everything. Used for the initial user which can add other users.');");
		DB::update("INSERT INTO ".$this->tablename." (name, type, description) VALUES('every_net', 'client', 'Is allowed to access every net. Used for the initial user which can add other users.');");

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
