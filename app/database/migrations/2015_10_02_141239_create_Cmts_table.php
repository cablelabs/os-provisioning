<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Models\Cmts;

class CreateCmtsTable extends Migration {

	// name of the table to create
	private $tablename = "cmts";

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
			$table->string('type');
				$this->fim->add("type");
			$table->string('ip');		// bundle ip
				$this->fim->add("ip");
			$table->string('community_rw');
				$this->fim->add("community_rw");
			$table->string('community_ro');
				$this->fim->add("community_ro");
			$table->string('company');
				$this->fim->add("company");
			$table->integer('network');
			$table->integer('state');
			$table->integer('monitoring');
			$table->timestamps();		// created_at and updated_at
		});

		// create FULLTEXT index including the given
		$this->fim->make_index();

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
		$c = Cmts::first();
		if ($c) $c->del_cmts_includes();

		Schema::drop($this->tablename);

		// remove all through dhcpCommand created cmts config files
		$files = glob('/etc/dhcp/nms/cmts_gws/*');		// get all files in dir
		foreach ($files as $file)
		{
			if(is_file($file))
			unlink($file);
		}
	}

}
