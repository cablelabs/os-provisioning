<?php

class RemoveCmtsAbilities extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        App\Ability::where('entity_type', Modules\ProvBase\Entities\CMTS::class)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
