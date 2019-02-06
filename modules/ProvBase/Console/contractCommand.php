<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Contract;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class contractCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contract Scheduling Command (call with daily or monthly)';

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
        if (! ($this->argument('date') == 'daily' || $this->argument('date') == 'monthly')) {
            return false;
        }

        $cs = Contract::where(whereLaterOrEqualThanDate('contract_end', date('Y-m-d')))->get();

        $i = 1;
        $num = count($cs);

        foreach ($cs as $c) {
            echo "contract month: $i/$num \r";
            $i++;

            if ($this->argument('date') == 'daily') {
                $c->daily_conversion();
            }

            if ($this->argument('date') == 'monthly') {
                $c->monthly_conversion();
            }
        }
        echo "\n";

        system('/bin/chown -R apache '.storage_path('logs'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['date', InputArgument::REQUIRED, 'daily/monthly'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        ];
    }
}
