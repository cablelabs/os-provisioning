<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModemTable extends Migration {

	// name of the table to create
	private $tablename = "modem";

	function __construct() {

		// get and instanciate of index maker
		require_once(getcwd()."/app/extensions/database/FulltextIndexMaker.php");
		$this->fim = new FulltextIndexMaker($this->tablename);
	}

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// creates directory for modem config files and changes owner
		$dir = '/tftpboot/cm';
		if(!is_dir($dir))
			mkdir ($dir, '0755');
		system('/bin/chown -R apache /tftpboot/cm');
		system('/bin/chown -R apache /etc/dhcp/');

		Schema::create($this->tablename, function(Blueprint $table)
		{
			$table->engine = 'MyISAM'; // InnoDB doesn't support fulltext index in MariaDB < 10.0.5
			$table->increments('id');
			$table->string('name');
				$this->fim->add("name");
			$table->string('hostname');
				$this->fim->add("hostname");
			$table->integer('contract_id')->unsigned();
			$table->string('mac')->sizeof(17);
				$this->fim->add("mac");
			$table->integer('status');
			$table->boolean('public');
			$table->boolean('network_access');
			$table->string('serial_num');
				$this->fim->add("serial_num");
			$table->string('inventar_num');
				$this->fim->add("inventar_num");
			$table->text('description');
				$this->fim->add("description");
			$table->integer('parent');
			$table->integer('configfile_id')->unsigned();
			$table->integer('qos_id')->unsigned();
			$table->timestamps();
		});

		// create FULLTEXT index including the given
		$this->fim->make_index();

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
