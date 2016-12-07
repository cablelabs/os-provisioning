<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameTreeToNetElementTable extends BaseMigration {

	// name of the table to update
	protected $tablename = "tree";


	/**
	 * Run the migrations - Rename tree to netelement and merge both tables
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('device');

		Schema::rename($this->tablename, 'netelement');

		// add fields to merge tables
		Schema::table('netelement', function(Blueprint $table)
		{
			$table->integer('devicetype_id')->unsigned();
			$table->integer('netelementtype_id')->unsigned();
			$table->string('community_ro', 45);
			$table->string('community_rw', 45);
			$table->string('address1');
			$table->string('address2');
			$table->string('address3');
			$table->dropColumn(['type', 'type_new', 'tp', 'tp_new', 'state', 'state_new', 'parent']);
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
		Schema::rename('netelement', 'tree');

		Schema::create('device', function(Blueprint $table)
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

		// It's possible to keep the data here, but because netelement is not really usable in this state it should be too much effort for nothing

		Schema::table('tree', function(Blueprint $table)
		{
			$table->string('type');
			$table->integer('type_new')->unsigned();
			$table->string('tp', 8);
			$table->integer('tp_new');
			$table->string('state');
			$table->integer('state_new');
			$table->integer('parent');
			$table->dropColumn(['devicetype_id', 'netelementtype_id', 'community_ro', 'community_rw', 'address1', 'address2', 'address3']);
		});
	}

}