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

			$table->string('user_id')->index();
			$table->string('meta_id')->index();
		});

		DB::update("INSERT INTO ".$this->tablename." (user_id, meta_id) VALUES(1, 1);");
		DB::update("INSERT INTO ".$this->tablename." (user_id, meta_id) VALUES(1, 2);");
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

