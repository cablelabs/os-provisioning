<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractQosidFieldTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contract', function(Blueprint $table)
		{
			$table->renameColumn('qos_id', 'price_id');
			$table->renameColumn('next_qos_id', 'next_price_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('', function(Blueprint $table)
		{
			$table->renameColumn('price_id', 'qos_id');
			$table->renameColumn('next_price_id', 'next_qos_id');
		});
	}

}