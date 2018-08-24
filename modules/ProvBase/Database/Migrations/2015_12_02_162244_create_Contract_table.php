<?php

use Illuminate\Database\Schema\Blueprint;

class CreateContractTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'contract';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('number')->unsigned();
            $table->string('number2', 32);
            $table->string('company');
            $table->string('salutation');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('street');
            $table->string('zip', 16);
            $table->string('city');
            $table->integer('country_id')->unsigned();
            $table->float('x');
            $table->float('y');
            $table->string('phone', 100);
            $table->string('fax', 100);
            $table->string('email');
            $table->date('birthday');
            $table->date('contract_start');
            $table->date('contract_end');
            $table->boolean('network_access');
            $table->integer('qos_id')->unsigned();
            $table->integer('next_qos_id')->unsigned();			// Note: only needed for NMS without Billing
            $table->integer('voip_id')->unsigned();
            $table->integer('next_voip_id')->unsigned();		// Note: only needed for NMS without Billing
            $table->string('sepa_iban', 34);
            $table->string('sepa_bic', 11);
            $table->string('sepa_holder');
            $table->string('sepa_institute');
            $table->boolean('create_invoice');
            $table->string('login', 32);
            $table->string('password', 32);
            $table->integer('net');
            $table->integer('cluster');
            // TODO/NOTE: for contracts with modems in different networks/clusters we need a separate N-to-M table
            $table->text('description');
        });

        $this->set_fim_fields([
            'number2',
            'company',
            'firstname',
            'lastname',
            'street',
            'zip',
            'city',
            'phone',
            'fax',
            'email',
            'description',
            'sepa_iban',
        ]);
        $this->set_auto_increment(500000);

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
