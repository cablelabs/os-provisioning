<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Updater to add col for technical method of purchase tariffs
 *
 * @author Patrick Reichel
 */
class UpdatePhoneTariffTableAddMethodCol extends BaseMigration
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
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->enum('voip_protocol', ['MGCP', 'SIP'])->nullable();
        });

        $this->set_fim_fields([
            'external_identifier',
            'name',
            'description',
        ]);
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
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn('voip_protocol');
        });

        $this->set_fim_fields([
            'external_identifier',
            'name',
            'description',
        ]);
    }
}
