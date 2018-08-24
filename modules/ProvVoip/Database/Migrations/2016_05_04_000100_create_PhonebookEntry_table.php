<?php

use Illuminate\Database\Schema\Blueprint;

class CreatePhonebookEntryTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonebookentry';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('phonenumbermanagement_id')->unsigned();
            $table->char('reverse_search', 1);
            $table->char('publish_in_print_media', 1);
            $table->char('publish_in_electronic_media', 1);
            $table->char('directory_assistance', 1);
            $table->char('entry_type', 1);
            $table->char('publish_address', 1);

            $table->string('company');
            $table->string('academic_degree');
            $table->string('noble_rank');
            $table->string('nobiliary_particle');
            $table->string('lastname');
            $table->string('other_name_suffix');
            $table->string('firstname');

            $table->string('street');
            $table->string('houseno');
            $table->string('zipcode');
            $table->string('city');
            $table->string('urban_district');
            $table->string('business');
            $table->char('number_usage', 1);
            $table->string('tag');

            $table->date('external_creation_date')->nullable()->default(null);
            $table->date('external_update_date')->nullable()->default(null);
        });

        $this->set_fim_fields([
            'lastname',
            'firstname',
            'company',
            'noble_rank',
            'nobiliary_particle',
            'academic_degree',
            'other_name_suffix',
            'business',
            'street',
            'houseno',
            'zipcode',
            'city',
            'urban_district',
            'tag',
        ]);

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
