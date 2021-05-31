<?php

use Modules\HfcReq\Entities\NetElement;

class SetParentNullWhenDeletedOrNotexistingNetelementAtNetelementTable extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Set parent_id null for trashed netelements
        $deleted_netelements = NetElement::onlyTrashed()->get();
        NetElement::whereIn('parent_id', $deleted_netelements)
            ->whereNotNull('parent_id')
            ->update(['parent_id' => null]);

        // Set parent_id null for non existing netelements
        $netelements = NetElement::all();
        NetElement::whereNotIn('parent_id', $netelements->pluck('id'))
            ->whereNotNull('parent_id')
            ->update(['parent_id' => null]);

        // Fix tree
        NetElement::fixTree();
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
