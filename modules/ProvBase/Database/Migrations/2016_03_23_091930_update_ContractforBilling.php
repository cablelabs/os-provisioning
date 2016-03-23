<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractforBilling extends BaseMigration {

	protected $tablename = 'contract';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$bm = new \BaseModel;

		if (!$bm->module_is_active('Billingbase'))
			return;

		Schema::table('contract', function(Blueprint $table)
		{
			$table->integer('costcenter_id')->unsigned();
			// $table->dropColumn('sepa_iban');
			// $table->dropColumn('sepa_bic');
			// $table->dropColumn('sepa_holder');
			// $table->dropColumn('sepa_institute');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$bm = new \BaseModel;

		if (!$bm->module_is_active('Billingbase'))
			return;


		Schema::table('contract', function(Blueprint $table)
		{
			$table->dropColumn('costcenter_id');
		});
		
	}

}



