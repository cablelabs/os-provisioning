<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQosTable extends Migration {

	// name of the table to create
	private $tablename = "qos";

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
			$table->float('ds_rate_max');
			$table->float('us_rate_max');
			$table->integer('ds_rate_max_help')->unsigned();
			$table->integer('us_rate_max_help')->unsigned();
			$table->string('name');
				$this->fim->add("name");
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
