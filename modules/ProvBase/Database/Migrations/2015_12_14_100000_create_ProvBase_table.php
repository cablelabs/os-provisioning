<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProvBaseTable extends BaseMigration {

	// name of the table to create
	protected $tablename = 'provbase';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tablename, function(Blueprint $table)
		{
			$this->up_table_generic($table);

			$table->string('provisioning_server');
			$table->string('ro_community');
			$table->string('rw_community');
			$table->string('notif_mail');
			$table->string('domain_name');
			$table->integer('dhcp_def_lease_time')->unsigned();
			$table->integer('dhcp_max_lease_time')->unsigned();
			$table->integer('startid_contract')->unsigned();
			$table->integer('startid_modem')->unsigned();
			$table->integer('startid_endpoint')->unsigned();

		});

		return parent::up();
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
