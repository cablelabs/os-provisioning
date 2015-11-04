<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePhonenumberTable extends Migration {

	// name of the table to create
	private $tablename = "phonenumber";

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
			$table->integer('mta_id')->unsigned()->default(1);
			$table->tinyInteger('port')->unsigned();
			$table->enum('country_code', ['0049']);
			$table->string('prefix_number');
				$this->fim->add("prefix_number");
			$table->string('number');
				$this->fim->add("number");
			$table->string('username')->nullable();
				$this->fim->add("username");
			$table->string('password')->nullable();
				// => passwords not in index :-)
			$table->boolean('active');
			$table->boolean('is_dummy')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});

		// create FULLTEXT index including the given
		$this->fim->make_index();

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
