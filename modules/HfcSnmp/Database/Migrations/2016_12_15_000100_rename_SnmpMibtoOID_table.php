<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameSnmpMibtoOIDTable extends BaseMigration {

	// name of the table to update
	protected $tablename = "snmpmib";


	/**
	 * Run the migrations - Rename tree to netelement and merge both tables
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::rename($this->tablename, 'oid');

		Schema::table('oid', function(Blueprint $table)
		{
			// we need to use mysql statement here, as we have enums in this table and there are errors related to Doctrine DBAL issue 
			DB::statement('ALTER TABLE oid CHANGE devicetype_id mibfile_id int');
			DB::statement('ALTER TABLE oid modify html_type  enum(\'text\',\'select\',\'groupbox\',\'textarea\') null');
			DB::statement('ALTER TABLE oid modify type enum(\'i\',\'u\',\'s\',\'x\',\'d\',\'n\',\'o\',\'t\',\'a\',\'b\') null');
			// $table->renameColumn('devicetype_id', 'mibfile_id');
			$table->string('name');
			$table->string('syntax');
			$table->string('access');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::rename('oid', $this->tablename);

		Schema::table($this->tablename, function(Blueprint $table)
		{
			DB::statement('ALTER TABLE '.$this->tablename.' CHANGE mibfile_id devicetype_id int');
			$table->dropColumn(['name', 'syntax', 'access']);
			// NOTE: it's not desired to undo the not null modify statements
		});
	}

}