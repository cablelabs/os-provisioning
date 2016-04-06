<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractForEnviaTable extends Migration {

    /**
	 * Run the migrations.
	 * For using the Envia API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function(Blueprint $table)
        {
			$table->string('customer_number', 60)->after('number');
			$table->string('customer_external_id', 60)->nullable()->default(NULL)->after('customer_number');
			$table->string('contract_number', 60)->after('customer_external_id');
			$table->string('contract_external_id', 60)->nullable()->default(NULL)->after('contract_number');
			$table->date('contract_ext_creation_date')->nullable()->default(NULL)->after('contract_external_id');
			$table->date('contract_ext_termination_date')->nullable()->default(NULL)->after('contract_ext_creation_date');
			$table->string('house_number', 8)->after('street');
			$table->string('password', 64)->change();
        });
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
			$table->dropColumn([
				'customer_number',
				'customer_external_id',
				'contract_number',
				'contract_external_id',
				'contract_ext_creation_date',
				'contract_ext_termination_date',
				'house_number',
			]);

			$table->string('password', 32)->change();
        });
    }

}
