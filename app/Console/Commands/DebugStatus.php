<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DebugStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nms:debug-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for laravel debug mode and returns it in a format to be consumed by Nagios/Icinga.';

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
    public function handle()
    {
        if (config('app.debug')) {
            echo "WARNING - Laravel debug mode is enabled\n";

            return 1;
        }

        echo "OK - Laravel debug mode is disabled\n";

        return 0;
    }
}
