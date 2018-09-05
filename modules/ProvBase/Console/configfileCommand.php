<?php

namespace Modules\provbase\Console;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Modem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\ProvBase\Entities\Configfile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class configfileCommand extends Command implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'nms:configfile {filter?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make all configfiles';

    /**
     * Configfile ID
     * 0:  all Modem & MTA Configfiles (CFs) are built
     * >0: all related (with children CFs) cfg's are built
     *
     * @var int
     */
    protected $cf_id = 0;

    /**
     * Filter (from argument) to only build cable modem or mta configfiles
     *
     * @var string 		cm|mta
     */
    protected $filter = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($cf_id = 0, $filter = '')
    {
        $this->cf_id = $cf_id;
        $this->filter = $filter;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::debug(__CLASS__." called with configfile id: $this->cf_id");

        // handle configfile observer functionality via job in background
        if ($this->cf_id) {
            $cf = Configfile::find($this->cf_id);

            $cf->build_corresponding_configfiles();
            $cf->search_children(1);

            return;
        }

        if ($this->input && $this->argument('filter')) {
            $this->filter = $this->argument('filter');
        }

        // Modem
        if (! $this->filter || $this->filter == 'cm') {
            $cms = Modem::all();
            $this->_make_configfiles($cms, 'cm');
        }

        // MTA
        if (! $this->filter || $this->filter == 'mta') {
            if (! \Module::collections()->has('ProvVoip')) {
                return;
            }

            $mtas = \Modules\ProvVoip\Entities\Mta::all();
            $this->_make_configfiles($mtas, 'mta');
        }
    }

    /**
     * @param array  Objects of Modem or Mta
     */
    private function _make_configfiles($devices, $type)
    {
        $i = 1;
        $num = count($devices);
        $type = strtoupper($type);

        \Log::info("Build all $num $type configfiles");

        foreach ($devices as $device) {
            echo "$type: create config files: $i/$num \r";
            $i++;
            $device->make_configfile();
        }

        echo "\n";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // array('filter', InputArgument::OPTIONAL, 'Build only Configfiles of CMs or MTAs (cm|mta)'),
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
            // array('configfile_id', null, InputOption::VALUE_OPTIONAL, 'ID of Configfile - build all related CMs and MTAs for that and all children CFs, e.g. 1', 0),
        ];
    }
}
