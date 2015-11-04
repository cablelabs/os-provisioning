<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigfileTable extends Migration {

	// name of the table to create
	private $tablename = "configfile";

	function __construct() {

		// get and instanciate of index maker
		require_once(getcwd()."/app/database/helpers/fulltext_index.php");
		$this->fim = new FullindexMaker($this->tablename);
	}

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
				$this->fim->add("name");
			$table->text('text');
				$this->fim->add("text");
			$table->enum('type', array('generic', 'network', 'vendor', 'user'));
			$table->enum('device', array('cm', 'mta'));
			$table->enum('public', array('yes', 'no'));
			$table->integer('parent_id')->unsigned();
			$table->string('firmware')->default("");
				$this->fim->add("firmware");
			$table->boolean('is_dummy')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});

		// create FULLTEXT index including the given
		$this->fim->make_index();

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
