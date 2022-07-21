<?php

use Modules\HfcReq\Entities\NetElementType;

class ChangeNetelementtypeToNestedSet extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NetElementType::fixTree();

        foreach (NetElementType::all() as $netElementType) {
            $netElementType->netelements()->update(['base_type_id' => $netElementType->base_type_id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
