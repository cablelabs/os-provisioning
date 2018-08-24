<?php

namespace Modules\NmsMail\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class MailDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('Modules\NmsMail\Database\Seeders\EmailTableSeeder');
    }
}
