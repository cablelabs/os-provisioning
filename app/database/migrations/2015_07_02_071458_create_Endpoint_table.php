<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEndpointTable extends Migration {

	// name of the table to create
	private $tablename = "endpoint";

	// array for fields to be in the FULLTEXT index (only types char, string and text!)
	private $index = array();

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tablename, function(Blueprint $table)
		{
			$table->engine = 'MyISAM'; // InnoDB doesn't support fulltext index in MariaDB < 10.0.5
			$table->increments('id');
			$table->string('hostname');
				array_push($this->index, "hostname");
			$table->string('name');
				array_push($this->index, "name");
			$table->string('mac',17);
			$table->text('description');
				array_push($this->index, "description");
			$table->enum('type', array('cpe','mta'));
			$table->boolean('public');
			// $table->integer('modem_id')->unsigned(); // depracted
			$table->timestamps();
		});

		// add fulltext index for all given fields
		if (isset($this->index) && (count($this->index) > 0)) {
			DB::statement("CREATE FULLTEXT INDEX ".$this->tablename."_all ON ".$this->tablename." (".implode(', ', $this->index).")");
		}

		DB::update("ALTER TABLE ".$this->tablename." AUTO_INCREMENT = 200000;");
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
