<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractforBillingTable extends BaseMigration {

	protected $tablename = 'contract';
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
			$table->renameColumn('voip_id', 'voip_price_id');
			$table->renameColumn('next_voip_id', 'next_voip_price_id');
			$table->integer('tv_price_id')->unsigned();
			$table->integer('next_tv_price_id')->unsigned();
			$table->integer('costcenter_id')->unsigned();
		});

		// NOTE: need to use this method because renaming columns of tables that have enums inside is not yet supported
		// DB::update('ALTER TABLE contract CHANGE voip_id voip_price_id ENUM("", "Flat", "Basic") NOT NULL');
		// DB::update('ALTER TABLE contract CHANGE next_voip_id next_voip_price_id integer NOT NULL');
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
			// $table->dropColumn('price_id');
			// $table->integer('qos_id');
			$table->renameColumn('next_price_id', 'next_qos_id');
			// $table->dropColumn('next_price_id');
			// $table->integer('next_qos_id');
			$table->renameColumn('voip_price_id', 'voip_id');
			$table->renameColumn('next_voip_price_id', 'next_voip_id');

			$table->dropColumn('tv_price_id');
			$table->dropColumn('next_tv_price_id');
			$table->dropColumn('costcenter_id');
		});
		
	}

}



