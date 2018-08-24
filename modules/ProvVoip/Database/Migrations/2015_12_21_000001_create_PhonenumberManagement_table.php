<?php

use Illuminate\Database\Schema\Blueprint;

class CreatePhonenumberManagementTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonenumbermanagement';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('phonenumber_id')->unsigned()->default(1);
            $table->integer('trcclass')->unsigned();
            $table->date('order_date');
            $table->dateTime('voipaccount_ext_creation_date')->nullable()->default(null);
            $table->date('activation_date');
            $table->boolean('porting_in')->default(0);
            $table->string('carrier_in', 16)->nullable;
            $table->date('deactivation_date');
            $table->dateTime('voipaccount_ext_termination_date')->nullable()->default(null);
            $table->boolean('porting_out')->default(0);
            $table->string('carrier_out', 16)->nullable;

            $table->string('subscriber_company');
            $table->string('subscriber_department');
            $table->string('subscriber_salutation');
            $table->string('subscriber_academic_degree');
            $table->string('subscriber_firstname');
            $table->string('subscriber_lastname');
            $table->string('subscriber_street');
            $table->string('subscriber_house_number', 8);
            $table->string('subscriber_zip', 16);
            $table->string('subscriber_city');
            $table->integer('subscriber_country')->unsigned();
        });

        $this->set_fim_fields(['subscriber_company',
            'subscriber_firstname',
            'subscriber_lastname',
            'subscriber_street',
            'subscriber_house_number',
            'subscriber_zip',
            'subscriber_city',
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
