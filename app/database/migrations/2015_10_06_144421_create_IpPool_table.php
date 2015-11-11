<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpPoolTable extends Migration {

	// name of the table to create
	private $tablename = "ippool";

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
		Schema::create($this->tablename, function(Blueprint $table)
		{
			$table->engine = 'MyISAM'; // InnoDB doesn't support fulltext index in MariaDB < 10.0.5
			$table->increments('id');
			$table->integer('cmts_id')->unsigned();
			$table->enum('type', array('CM', 'CPEPub', 'CPEPriv', 'MTA')); 	// (cm, cpePub, cpePriv, mta)
			$table->string('net')->sizeof(20);
				$this->fim->add("net");
			$table->string('netmask')->sizeof(20);
				$this->fim->add("netmask");
			$table->string('ip_pool_start')->sizeof(20);
				$this->fim->add("ip_pool_start");
			$table->string('ip_pool_end')->sizeof(20);
				$this->fim->add("ip_pool_end");
			$table->string('router_ip')->sizeof(20);
				$this->fim->add("router_ip");
			$table->string('broadcast_ip')->sizeof(20);
				$this->fim->add("broadcast_ip");
			$table->string('dns1_ip')->sizeof(20);
				$this->fim->add("dns1_ip");
			$table->string('dns2_ip')->sizeof(20);
				$this->fim->add("dns2_ip");
			$table->string('dns3_ip')->sizeof(20);
				$this->fim->add("dns3_ip");
			$table->text('optional');
				$this->fim->add("optional");
			$table->timestamps();
		});

		// create FULLTEXT index including the given
		$this->fim->make_index();

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

