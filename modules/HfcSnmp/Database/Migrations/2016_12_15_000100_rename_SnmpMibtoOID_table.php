<?php

use Illuminate\Database\Schema\Blueprint;

class RenameSnmpMibtoOIDTable extends BaseMigration
{
    // name of the table to update
    protected $tablename = 'oid';

    /**
     * Run the migrations - Rename tree to netelement and merge both tables
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('snmpmib', $this->tablename);

        Schema::table('oid', function (Blueprint $table) {
            // we need to use mysql statement here, as we have enums in this table and there are errors related to Doctrine DBAL issue
            DB::statement('ALTER TABLE oid CHANGE devicetype_id mibfile_id int');
            DB::statement('ALTER TABLE oid modify html_type  enum(\'text\',\'select\',\'groupbox\',\'textarea\') null');
            DB::statement('ALTER TABLE oid modify type enum(\'i\',\'u\',\'s\',\'x\',\'d\',\'n\',\'o\',\'t\',\'a\',\'b\') null');
            DB::statement('ALTER TABLE oid CHANGE field name VARCHAR(255)');

            $table->string('name_gui'); 			// Better understandable Name in Controlling View
            $table->integer('unit_divisor')->nullable();
            $table->integer('startvalue')->nullable();
            $table->integer('endvalue')->nullable();
            $table->integer('stepsize')->nullable();
            $table->string('syntax');
            $table->string('access');

            $table->text('value_set')->nullable(); 				// Possible Values for Select

            // move to pivot table (of many to many relationship) - NOTE: Schema again is erroneous here
            DB::statement('ALTER TABLE oid drop html_frame, drop html_properties, drop html_id');
            // $table->dropColumn(['html_frame', 'html_properties', 'html_id']);
        });

        $this->set_fim_fields(['name', 'name_gui', 'syntax', 'value_set', 'description']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename($this->tablename, 'snmpmib');

        Schema::table('snmpmib', function (Blueprint $table) {
            DB::statement('ALTER TABLE snmpmib CHANGE mibfile_id devicetype_id int');

            $dropColumns = ['name', 'syntax', 'access', 'name_gui', 'unit_divisor', 'startvalue', 'endvalue'];
            foreach ($dropColumns as $col) {
                DB::statement("ALTER TABLE snmpmib drop $col");
            }

            // NOTE: it's not desired to undo the not null modify statements

            $table->string('html_frame', 16);
            $table->text('html_properties');
            $table->integer('html_id')->unsigned(); // for future use
        });
    }
}
