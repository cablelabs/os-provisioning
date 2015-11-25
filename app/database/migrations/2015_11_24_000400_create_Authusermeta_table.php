<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthusermetaTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "authusermeta";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create($this->tablename, function(Blueprint $table) {

			$this->up_table_generic($table);

			$table->string('user_id');
			$table->string('meta_id');
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

