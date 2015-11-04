<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMtaTable extends Migration {

	// name of the table to create
	private $tablename = "mta";

	// array for fields to be in the FULLTEXT index (only types char, string and text!)
	private $index = array();

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$dir = '/tftpboot/mta';
		if(!is_dir($dir))
			mkdir ($dir, '0755');

		Schema::create($this->tablename, function(Blueprint $table)
		{
			$table->engine = 'MyISAM'; // InnoDB doesn't support fulltext index in MariaDB < 10.0.5
			$table->increments('id');
			$table->integer('modem_id')->unsigned()->default(1);
			$table->string('mac', 17);
				array_push($this->index, "mac");
			$table->string('hostname');
				array_push($this->index, "hostname");
			$table->integer('configfile_id')->unsigned()->default(1);
			$table->enum('type', ['sip','packetcable']);
			$table->boolean('is_dummy')->default(0);
			$table->timestamps();
			$table->softDeletes();

		});

		// add fulltext index for all given fields
		if (isset($this->index) && (count($this->index) > 0)) {
			DB::statement("CREATE FULLTEXT INDEX ".$this->tablename."_all ON ".$this->tablename." (".implode(', ', $this->index).")");
		}

		# insert a dummy mta for each type
		$enum_types = array(
			1 => 'sip',
			2 => 'packetcable',
		);

		foreach($enum_types as $i => $v) {
			DB::update("INSERT INTO ".$this->tablename." (hostname, type, is_dummy, deleted_at) VALUES('dummy-mta-".$v."',".$i.",1,NOW());");
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
