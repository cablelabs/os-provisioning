<?php

use Modules\ProvBase\Entities\Contract;

class UpdateContractSetNullableColumnsToNull extends BaseMigration
{
    public $migrationScope = 'database';

    protected $tablename = 'contract';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Contract::where('qos_id', 0)->update(['qos_id' => null]);
        Contract::where('next_qos_id', 0)->update(['next_qos_id' => null]);
        Contract::where('voip_id', 0)->update(['voip_id' => null]);
        Contract::where('next_voip_id', 0)->update(['next_voip_id' => null]);
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
