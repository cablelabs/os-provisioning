<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthcoreTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "authcore";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create($this->tablename, function(Blueprint $table) {

			$this->up_table_generic($table);

			$table->string('core');
			$table->enum('type', array('model', 'net'));
			$table->string('description');
		});
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
