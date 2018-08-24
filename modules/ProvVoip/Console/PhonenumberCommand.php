<?php

namespace Modules\ProvVoip\Console;

use Illuminate\Console\Command;
use Modules\ProvVoip\Entities\Phonenumber;

class PhonenumberCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'provvoip:phonenumber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Phonenumber Scheduling Command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $phonenumbers = Phonenumber::all();

        foreach ($phonenumbers as $phonenumber) {
            $phonenumber->daily_conversion();
        }
    }
}
