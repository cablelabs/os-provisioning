<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


/**
 * This is used as Pivot Table of the Many-to-Many Relationship between OIDs & NetElementTypes
 *
 * It adds extra information like it's done in Item (pivot of contract & product)
 */
class CreateParameterTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "parameter";


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tablename, function(Blueprint $table)
		{
			$this->up_table_generic($table);

			$table->integer('netelementtype_id')->unsigned();
			$table->integer('oid_id')->unsigned();

			$table->string('html_frame',16);
			$table->text('html_properties');
			$table->integer('html_id')->unsigned(); // for future use
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
		Schema::drop($this->tablename);
	}

}
