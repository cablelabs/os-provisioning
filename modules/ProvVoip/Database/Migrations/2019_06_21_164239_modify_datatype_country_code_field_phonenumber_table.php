<?php

class ModifyDatatypeCountryCodeFieldPhonenumberTable extends BaseMigration
{
    protected $tablename = 'phonenumber';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE $this->tablename MODIFY COLUMN country_code varchar (191);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE $this->tablename MODIFY COLUMN country_code enum ('0049');");
    }
}
