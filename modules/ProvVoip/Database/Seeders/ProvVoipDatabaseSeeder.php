<?php

namespace Modules\Provvoip\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ProvVoipDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('Modules\ProvVoip\Database\Seeders\ProvVoipConfigTableSeeder');
        $this->call('Modules\ProvVoip\Database\Seeders\MtaTableSeeder');
        $this->call('Modules\ProvVoip\Database\Seeders\PhoneTariffTableSeeder');
        $this->call('Modules\ProvVoip\Database\Seeders\PhonenumberTableSeeder');
        $this->call('Modules\ProvVoip\Database\Seeders\PhonenumberManagementTableSeeder');
    }
}
