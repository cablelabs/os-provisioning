<?php

use Database\Migrations\BaseMigration;
use Modules\HfcReq\Entities\NetElementType;

return new class extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NetElementType::create([
            'id' => 15,
            'name' => 'RKM-Server',
            'vendor' => 'SAT-Kabel',
            'base_type_id' => 15,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        NetElementType::where('id', 15)->forceDelete();
    }
};
