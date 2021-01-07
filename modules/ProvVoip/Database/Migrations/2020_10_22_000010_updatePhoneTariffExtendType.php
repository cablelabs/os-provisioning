<?php

use Modules\ProvVoip\Entities\PhoneTariff;

class UpdatePhoneTariffExtendType extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'phonetariff';

    /**
     * Run the migrations.
     *
     * @author Nino Ryschawy
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE phonetariff MODIFY COLUMN type ENUM('purchase', 'sale', 'basic', 'landlineflat', 'allnetflat') NOT NULL");

        $phonetariffs = [
            0 => [
                'name' => 'Basic',
                'type' => 'basic',
                'usable' => 1,
            ],
            1 => [
                'name' => 'Landline flat',
                'type' => 'landlineflat',
                'usable' => 1,
            ],
            2 => [
                'name' => 'Allnetflat',
                'type' => 'allnetflat',
                'usable' => 1,
            ],
        ];

        if (! PhoneTariff::count()) {
            foreach ($phonetariffs as $pt) {
                PhoneTariff::create($pt);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @author Nino Ryschawy
     *
     * @return void
     */
    public function down()
    {
        PhoneTariff::whereIn('type', ['basic', 'landlineflat', 'allnetflat'])->delete();

        DB::statement("ALTER TABLE phonetariff MODIFY COLUMN type ENUM('purchase', 'sale') NOT NULL");
    }
}
