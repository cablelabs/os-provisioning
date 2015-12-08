<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "device";


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(strtolower('Device'), function(Blueprint $table)
		{
			$this->up_table_generic($table);

			$table->integer('devicetype_id')->unsigned();
			$table->string('name');
			$table->string('ip', 15);
			$table->string('community_ro', 45);
			$table->string('community_rw', 45);
			$table->string('address1');
			$table->string('address2');
			$table->string('address3');
			$table->text('description');
		});

		return parent::up();
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop(strtolower('Device'));
	}

}
