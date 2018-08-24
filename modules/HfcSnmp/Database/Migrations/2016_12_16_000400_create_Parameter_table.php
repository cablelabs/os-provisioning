<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * This is used as Pivot Table of the Many-to-Many Relationship between OIDs & NetElementTypes
 *
 * It adds extra information like it's done in Item (pivot of contract & product)
 */
class CreateParameterTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'parameter';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('netelementtype_id')->unsigned();
            $table->integer('oid_id')->unsigned();
            $table->boolean('diff_param');				// Difference-Parameter: if checked show value as difference with request before
            $table->string('divide_by'); 				// divide value by added values of this oids that have to be existent in that list/frame/table

            // special extensions for Table-OIDs
            $table->integer('parent_id')->unsigned(); 	// If Set this is a SubOID, then only these SubOIDs will be considered for table view
            $table->boolean('third_dimension');			// checkbox for being a parameter that's in the list behind a table row/element

            // arrangement stuff in view layout
            $table->string('html_frame', 16);
            $table->text('html_properties');
            $table->integer('html_id')->unsigned()->nullable(); // for future use
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
