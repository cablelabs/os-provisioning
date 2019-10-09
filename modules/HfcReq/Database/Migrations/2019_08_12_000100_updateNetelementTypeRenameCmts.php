<?php

use Modules\HfcReq\Entities\NetElementType;

class UpdateNetelementTypeRenameCmts extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'netelementtype';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NetElementType::find(3)->update(['name' => 'NetGw']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        NetElementType::find(3)->update(['name' => 'Cmts']);
    }
}
