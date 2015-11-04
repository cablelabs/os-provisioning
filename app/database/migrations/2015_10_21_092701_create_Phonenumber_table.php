<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePhonenumberTable extends Migration {

	// name of the table to create
	private $tablename = "phonenumber";

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
			$table->integer('mta_id')->unsigned()->default(1);
			$table->tinyInteger('port')->unsigned();
			$table->enum('country_code', ['0049']);
			$table->string('prefix_number');
				array_push($this->index, "prefix_number");
			$table->string('number');
				array_push($this->index, "number");
			$table->string('username')->nullable();
				array_push($this->index, "username");
			$table->string('password')->nullable();
				// => passwords not in index :-)
			$table->boolean('active');
			$table->boolean('is_dummy')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});

		// add fulltext index for all given fields
		if (isset($this->index) && (count($this->index) > 0)) {
			DB::statement("CREATE FULLTEXT INDEX ".$this->tablename."_all ON ".$this->tablename." (".implode(', ', $this->index).")");
		}

		// insert dummy number
		DB::update("INSERT INTO ".$this->tablename." (prefix_number,number,active,is_dummy,deleted_at) VALUES('0000','00000',1,1,NOW());");
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
