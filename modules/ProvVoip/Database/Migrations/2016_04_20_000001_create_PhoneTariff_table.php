<?php

use Illuminate\Database\Schema\Blueprint;

class CreatePhoneTariffTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonetariff';

    /**
     * Run the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('external_identifier');		// at envia TEL this is a integer or a string…
            $table->string('name');						// name to show in forms
            $table->enum('type', ['purchase', 'sale']);
            $table->string('description');
            $table->boolean('usable')->default(1);		// there are more envia TEL variations as we really use (e.g. MGCP stuff) – can be used for temporary deactivation of tariffs or to prevent a tariff from being assingned again
        });

        $this->set_fim_fields([
            'external_identifier',
            'name',
            'description',
        ]);

        // add dummy tariffs to be overwritten by user
        DB::update('INSERT INTO '.$this->tablename." (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), 'n/a purchase', 'Dummy purchase tariff', 'purchase', 'Placeholder: Remove and add your own purchase tariffs', 1);");
        DB::update('INSERT INTO '.$this->tablename." (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), 'n/a sale', 'Dummy sale tariff', 'sale', 'Placeholder: Remove and add your own sale tariffs', 1);");

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @author Patrick Reichel
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
}
