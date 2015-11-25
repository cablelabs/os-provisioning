<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthmetacoreTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "authmetacore";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create($this->tablename, function(Blueprint $table) {

			$this->up_table_generic($table);

			$table->string('meta_id')->index();
			$table->string('core_id')->index();
			$table->boolean('view')->default(0);
			$table->boolean('add')->default(0);
			$table->boolean('edit')->default(0);
			$table->boolean('delete')->default(0);
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

