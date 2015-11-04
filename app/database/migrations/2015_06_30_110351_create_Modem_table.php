<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModemTable extends Migration {

	// name of the table to create
	private $tablename = "modem";

	// array for fields to be in the FULLTEXT index (only types char, string and text!)
	private $index = array();

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$dir = '/tftpboot/cm';
		if(!is_dir($dir))
			mkdir ($dir, '0755');

		Schema::create($this->tablename, function(Blueprint $table)
		{
			$table->engine = 'MyISAM'; // InnoDB doesn't support fulltext index in MariaDB < 10.0.5
			$table->increments('id');
			$table->string('name');
				array_push($this->index, "name");
			$table->string('hostname');
				array_push($this->index, "hostname");
			$table->integer('contract_id')->unsigned();
			$table->string('mac')->sizeof(17);
				array_push($this->index, "mac");
			$table->integer('status');
			$table->boolean('public');
			$table->boolean('network_access');
			$table->string('serial_num');
				array_push($this->index, "serial_num");
			$table->string('inventar_num');
				array_push($this->index, "inventar_num");
			$table->text('description');
				array_push($this->index, "description");
			$table->integer('parent');
			$table->integer('configfile_id')->unsigned();
			$table->integer('qos_id')->unsigned();
			$table->timestamps();
		});

		// add fulltext index for all given fields
		if (isset($this->index) && (count($this->index) > 0)) {
			DB::statement("CREATE FULLTEXT INDEX ".$this->tablename."_all ON ".$this->tablename." (".implode(', ', $this->index).")");
		}

		DB::update("ALTER TABLE ".$this->tablename." AUTO_INCREMENT = 100000;");
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
