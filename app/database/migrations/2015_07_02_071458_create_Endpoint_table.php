<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEndpointTable extends Migration {

	// name of the table to create
	private $tablename = "endpoint";

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
			$table->string('hostname');
				$this->fim->add("hostname");
			$table->string('name');
				$this->fim->add("name");
			$table->string('mac',17);
			$table->text('description');
				$this->fim->add("description");
			$table->enum('type', array('cpe','mta'));
			$table->boolean('public');
			// $table->integer('modem_id')->unsigned(); // depracted
			$table->timestamps();
		});

		// create FULLTEXT index including the given
		$this->fim->make_index();

		DB::update("ALTER TABLE ".$this->tablename." AUTO_INCREMENT = 200000;");
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
