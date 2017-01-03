<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Modules\HfcReq\Entities\NetElementType;

class RenameDeviceTypetoNetElementTypeTable extends BaseMigration {


	// name of the table to create
	protected $tablename = "netelementtype";


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::rename('devicetype', $this->tablename);		

		Schema::table($this->tablename, function(Blueprint $table)
		{
			$table->string('icon_name');
		});

		// Set Default Entries
		$defaults = ['Net', 'Cluster', 'Cmts', 'Amplifier', 'Node', 'Data'];

		foreach ($defaults as $d)
			NetElementType::create(['name' => $d]);

		return parent::up();
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table($this->tablename, function(Blueprint $table)
		{
			$table->dropColumn('icon_name');
		});

		Schema::rename($this->tablename, 'devicetype');
	}

}
