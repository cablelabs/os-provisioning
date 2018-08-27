<?php

namespace Modules\Hfcsnmp\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class HfcSnmpDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call("Modules\HfcSnmp\Database\Seeders\MibFileTableSeeder");
        $this->call("Modules\HfcSnmp\Database\Seeders\OIDTableSeeder");
        $this->call("Modules\HfcSnmp\Database\Seeders\ParameterTableSeeder");
    }
}
