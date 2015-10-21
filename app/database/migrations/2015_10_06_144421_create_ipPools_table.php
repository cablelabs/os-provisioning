<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpPoolsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ip_pools', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cmts_id')->unsigned();
			$table->enum('type', array('CM', 'CPEPub', 'CPEPriv', 'MTA')); 	// (cm, cpePub, cpePriv, mta)
			$table->string('net')->sizeof(20);
			$table->string('netmask')->sizeof(20);
			$table->string('ip_pool_start')->sizeof(20);
			$table->string('ip_pool_end')->sizeof(20);
			$table->string('router_ip')->sizeof(20);
			$table->string('broadcast_ip')->sizeof(20);
			$table->string('dns1_ip')->sizeof(20);
			$table->string('dns2_ip')->sizeof(20);
			$table->string('dns3_ip')->sizeof(20);
			$table->text('optional');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ip_pools');
	}

}

