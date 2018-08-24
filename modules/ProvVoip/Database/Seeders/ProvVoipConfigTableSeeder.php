<?php

namespace Modules\ProvVoip\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;
use Modules\ProvVoip\Entities\ProvVoip;

class ProvVoipConfigTableSeeder extends \BaseSeeder
{
    public function run()
    {
        ProvVoip::create([
            'startid_mta' => 300000,
        ]);
    }
}
