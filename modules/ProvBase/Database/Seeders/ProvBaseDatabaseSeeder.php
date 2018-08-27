<?php

namespace Modules\Provbase\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ProvBaseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('Modules\ProvBase\Database\Seeders\CmtsTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\IpPoolTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ConfigfileTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\QosTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ContractTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ModemTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\EndpointTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\ProvBaseConfigTableSeeder');
        $this->call('Modules\ProvBase\Database\Seeders\DomainTableSeeder');
    }
}
