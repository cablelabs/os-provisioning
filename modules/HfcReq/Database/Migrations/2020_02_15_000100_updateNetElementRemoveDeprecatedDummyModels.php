<?php

use Modules\HfcReq\Entities\NetElement;

class updateNetElementRemoveDeprecatedDummyModels extends RelationshipFixes
{
    public $migrationScope = 'database';

    /**
     * Remove deprecated dummy objects - edit page can not be opened anymore on these objects
     *
     *
     * @return void
     */
    public function up()
    {
        NetElement::where('id', 1)->where('name', '-unknown parent-')->delete();
        NetElement::where('id', 2)->where('name', '-root-')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        NetElement::where('id', 1)->where('name', '-unknown parent-')->update(['deleted_at' => null]);
        NetElement::where('id', 2)->where('name', '-root-')->update(['deleted_at' => null]);
    }
}
