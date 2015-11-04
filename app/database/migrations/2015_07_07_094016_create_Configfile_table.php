<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigfileTable extends Migration {

	// name of the table to create
	private $tablename = "configfile";

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
			$table->string('name');
				array_push($this->index, "name");
			$table->text('text');
				array_push($this->index, "text");
			$table->enum('type', array('generic', 'network', 'vendor', 'user'));
			$table->enum('device', array('cm', 'mta'));
			$table->enum('public', array('yes', 'no'));
			$table->integer('parent_id')->unsigned();
			$table->string('firmware')->default("");
				array_push($this->index, "firmware");
			$table->boolean('is_dummy')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});

		// add fulltext index for all given fields
		if (isset($this->index) && (count($this->index) > 0)) {
			DB::statement("CREATE FULLTEXT INDEX ".$this->tablename."_all ON ".$this->tablename." (".implode(', ', $this->index).")");
		}

		# insert a dummy for each enum value
		$enum_devices = array(
			1 => 'cm',
			2 => 'mta',
		);
		foreach($enum_devices as $i => $v) {
			DB::update("INSERT INTO ".$this->tablename." (name, device, is_dummy, deleted_at) VALUES('dummy-cfg-".$v."',".$i.",1,NOW());");
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop($this->tablename);

		// remove all config and firmware files
		$files = array();
		$files['cm'] = glob('/tftpboot/cm/*');              // get all files in dir
		$files['mta'] = glob('/tftpboot/mta/*');              // get all files in dir
		$files['fw'] = glob('/tftpboot/fw/*');              // get all files in dir

		foreach ($files as $type) {
			foreach ($type as $file) {
			if(is_file($file))
				unlink($file);
			}
		}
	}

}
