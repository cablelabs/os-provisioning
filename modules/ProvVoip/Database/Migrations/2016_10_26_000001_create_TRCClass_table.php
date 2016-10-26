<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTRCClassTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "trcclass";


	/**
	 * Run the migrations.
	 *
	 * ATTENTION: TRCClass has been moved from ProvVoipEnvia (needed on PhonenumberManagement in each case!)
	 *
	 * @return void
	 */
	public function up()
	{
		// this table needs to be initialized
		// as trc classes are defined by every provider we don't “seed” them here any longer
		// in case of Envia they are part of the inital_config.sql

		// as there could exist a table created on the old ProvVoipEnvia migration we have to check for this special case
		// do nothing in this case!
		if (!Schema::hasTable($this->tablename)) {
			Schema::create($this->tablename, function(Blueprint $table)
			{
				$this->up_table_generic($table);

				$table->integer('trc_id')->unsigned()->unique();
				$table->string('trc_short');
				$table->string('trc_description');
			});

			return parent::up();
		}

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// this table can be deleted from rolling back ProvVoipEnvia – so we have to check for existance at this point
		Schema::dropIfExists($this->tablename);
	}

}
