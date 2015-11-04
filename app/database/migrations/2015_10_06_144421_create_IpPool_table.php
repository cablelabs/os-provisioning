<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpPoolTable extends Migration {

	// name of the table to create
	private $tablename = "ippool";

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
			$table->integer('cmts_id')->unsigned();
			$table->enum('type', array('CM', 'CPEPub', 'CPEPriv', 'MTA')); 	// (cm, cpePub, cpePriv, mta)
			$table->string('net')->sizeof(20);
				array_push($this->index, "net");
			$table->string('netmask')->sizeof(20);
				array_push($this->index, "netmask");
			$table->string('ip_pool_start')->sizeof(20);
				array_push($this->index, "ip_pool_start");
			$table->string('ip_pool_end')->sizeof(20);
				array_push($this->index, "ip_pool_end");
			$table->string('router_ip')->sizeof(20);
				array_push($this->index, "router_ip");
			$table->string('broadcast_ip')->sizeof(20);
				array_push($this->index, "broadcast_ip");
			$table->string('dns1_ip')->sizeof(20);
				array_push($this->index, "dns1_ip");
			$table->string('dns2_ip')->sizeof(20);
				array_push($this->index, "dns2_ip");
			$table->string('dns3_ip')->sizeof(20);
				array_push($this->index, "dns3_ip");
			$table->text('optional');
				array_push($this->index, "optional");
			$table->timestamps();
		});

		// add fulltext index for all given fields
		if (isset($this->index) && (count($this->index) > 0)) {
			DB::statement("CREATE FULLTEXT INDEX ".$this->tablename."_all ON ".$this->tablename." (".implode(', ', $this->index).")");
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
	}

}

