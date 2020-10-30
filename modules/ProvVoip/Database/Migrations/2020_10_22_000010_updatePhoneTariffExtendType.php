<?php

use Illuminate\Database\Schema\Blueprint;
use Modules\ProvVoip\Entities\Phonetariff;

class UpdatePhoneTariffExtendType extends BaseMigration
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

        if (! Phonetariff::count()) {
            foreach ($phonetariffs as $pt) {
                Phonetariff::create($pt);
            }
        }
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
        Phonetariff::whereIn('type', ['basic', 'landlineflat', 'allnetflat'])->delete();

        DB::statement("ALTER TABLE phonetariff MODIFY COLUMN type ENUM('purchase', 'sale') NOT NULL");

    }
}
