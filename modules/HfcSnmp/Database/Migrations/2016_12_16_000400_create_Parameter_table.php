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

			// special extensions for Table-OIDs
			$table->integer('parent_id')->unsigned(); 	// If Set this is a SubOID, then only these SubOIDs will be considered for table view
			$table->boolean('3rd_dimension'); 			// checkbox for being a parameter that's in the list behind a table row/element
			$table->string('indices');

			// arrangement stuff in view layout
			$table->string('html_frame',16);
			$table->text('html_properties');
			$table->integer('html_id')->unsigned()->nullable(); // for future use
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
