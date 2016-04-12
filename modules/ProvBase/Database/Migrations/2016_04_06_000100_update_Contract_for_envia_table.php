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
			$table->string('number', 32)->change();	// was integer in original migration
			$table->string('number3', 32)->after('number2');
			$table->string('number4', 32)->after('number3');
			$table->string('contract_external_id', 60)->nullable()->default(NULL)->after('number4');
			$table->string('customer_external_id', 60)->nullable()->default(NULL)->after('contract_external_id');
			$table->date('contract_ext_creation_date')->nullable()->default(NULL)->after('customer_external_id');
			$table->date('contract_ext_termination_date')->nullable()->default(NULL)->after('customer_ext_creation_date');
			$table->string('academic_degree')->after('salutation');
			$table->string('house_number', 8)->after('street');
			$table->boolean('phonebook_entry')->after('internet_access');
			$table->string('password', 64)->change();
        });

		$this->set_fim_fields([
			'number3',
			'number4',
			'contract_external_id',
			'customer_external_id',
			'academic_degree',
			'house_number',
		]);

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
				'number3',
				'number4',
				'contract_external_id',
				'customer_external_id',
				'contract_ext_creation_date',
				'contract_ext_termination_date',
				'academic_degree',
				'house_number',
				'phonebook_entry',
			]);

			$table->string('password', 32)->change();
        });
    }

}
