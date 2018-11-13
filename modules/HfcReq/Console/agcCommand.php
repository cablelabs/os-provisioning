<?php

namespace Modules\HfcReq\Console;

use Illuminate\Console\Command;
use Modules\HfcReq\Entities\NetElement;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class agcCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:agc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute Automatic Gain Control based on measured SNR';

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
     * Execute the console command. Automatically adjust power level of all CMTS
     *
     * @return true
     * @author: Ole Ernst
     */
    public function fire()
    {
        // get all clusters having a non-null AGC offset
        foreach (NetElement::where('netelementtype_id', 2)->whereNotNull('agc_offset')->get() as $netelement) {
            $netelement->apply_agc();
        }

        return true;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // array('example', InputArgument::REQUIRED, 'An example argument.'),
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
