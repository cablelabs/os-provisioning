<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMtaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// creates directory for mta config files and changes owner
		$dir = '/tftpboot/mta';
		if(!is_dir($dir))
			mkdir ($dir, '0755');
		system('/bin/chown -R apache /tftpboot/mta');

		Schema::create('mta', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('modem_id')->unsigned()->default(1);
			$table->string('mac', 17);
			$table->string('hostname');
			$table->integer('configfile_id')->unsigned()->default(1);
			$table->enum('type', ['sip','packetcable']);
			$table->boolean('is_dummy')->default(0);
			$table->timestamps();
			$table->softDeletes();

		});

		# insert a dummy mta for each type
		$enum_types = array(
			1 => 'sip',
			2 => 'packetcable',
		);
		
		foreach($enum_types as $i => $v) {
			DB::update("INSERT INTO mta (hostname, type, is_dummy, deleted_at) VALUES('dummy-mta-".$v."',".$i.",1,NOW());");
		}

		DB::update("ALTER TABLE mta AUTO_INCREMENT = 100000;");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mta');
	}

}
