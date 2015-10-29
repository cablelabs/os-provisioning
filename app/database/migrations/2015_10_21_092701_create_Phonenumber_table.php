<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePhonenumberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('phonenumber', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('mta_id')->unsigned()->default(1);
			$table->tinyInteger('port')->unsigned();
			$table->enum('country_code', ['0049']);
			$table->string('prefix_number');
			$table->string('number');
			$table->string('username')->nullable();
			$table->string('password')->nullable();
			$table->boolean('active');
			$table->boolean('is_dummy')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});

		// insert dummy number
		DB::update("INSERT INTO phonenumber (prefix_number,number,active,is_dummy,deleted_at) VALUES('0000','00000',1,1,NOW());");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('phonenumber');
	}

}
