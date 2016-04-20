<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdatePhonenumberManagementForPhonebookEntriesTable extends BaseMigration {

	// name of the table to create
	protected $tablename = "phonenumbermanagement";


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table($this->tablename, function(Blueprint $table)
		{
			$table->boolean('phonebook_entry')->default(0);
			$table->boolean('reverse_search')->default(0);
			$table->string('phonebook_publish_in_print_media');
			$table->string('phonebook_publish_in_electronic_media');
			$table->string('phonebook_directory_assistance');
			$table->string('phonebook_entry_type');
			$table->string('phonebook_publish_address');

			$table->string('phonebook_company');
			$table->string('phonebook_academic_degree');
			$table->string('phonebook_noble_rank');
			$table->string('phonebook_nobiliary_particle');
			$table->string('phonebook_lastname');
			$table->string('phonebook_other_name_suffix');
			$table->string('phonebook_firstname');

			$table->string('phonebook_street');
			$table->string('phonebook_houseno');
			$table->string('phonebook_zipcode');
			$table->string('phonebook_city');
			$table->string('phonebook_urban_district');
			$table->string('phonebook_business');
			$table->string('phonebook_usage');
			$table->string('phonebook_tag');
		});

		$this->set_fim_fields([
			'subscriber_company',
			'subscriber_firstname',
			'subscriber_lastname',
			'subscriber_street',
			'subscriber_house_number',
			'subscriber_zip',
			'subscriber_city',

			'phonebook_lastname',
			'phonebook_firstname',
			'phonebook_company',
			'phonebook_noble_rank',
			'phonebook_nobiliary_particle',
			'phonebook_academic_degree',
			'phonebook_other_name_suffix',
			'phonebook_business',
			'phonebook_street',
			'phonebook_houseno',
			'phonebook_zipcode',
			'phonebook_city',
			'phonebook_urban_district',
			'phonebook_usage',
			'phonebook_tag',
		]);

		$this->set_auto_increment(300000);

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
