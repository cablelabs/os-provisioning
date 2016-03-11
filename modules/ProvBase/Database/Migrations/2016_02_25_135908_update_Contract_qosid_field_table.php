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

		// Note: need to use this method because laravel has bugs on changing column type
		DB::update('ALTER TABLE contract CHANGE voip_id voip_tariff ENUM("", "Flat", "Basic") NOT NULL');
		DB::update('ALTER TABLE contract CHANGE next_voip_id next_voip_tariff ENUM("", "Flat", "Basic") NOT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contract', function(Blueprint $table)
		{
			$table->renameColumn('price_id', 'qos_id');
			$table->renameColumn('next_price_id', 'next_qos_id');
		});
		
		DB::update('ALTER TABLE contract CHANGE voip_tariff voip_id INTEGER NOT NULL');
		DB::update('ALTER TABLE contract CHANGE next_voip_tariff next_voip_id INTEGER NOT NULL');
	}

}