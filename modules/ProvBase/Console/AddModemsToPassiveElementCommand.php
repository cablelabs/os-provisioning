<?php

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Modem;
use Modules\HfcReq\Entities\NetElement;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\HfcCustomer\Http\Controllers\CustomerTopoController;

class AddModemsToPassiveElementCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:mtpe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a bunch of Modems to a passive Netelement';

    /**
     * The Passive Netelement the modem get added to
     *
     * @var Netelement
     */
    protected $netelement;

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
        $this->netelement = NetElement::findOrFail($this->argument('netelement'));

        if ($this->netelement->netelementtype->name != 'Passive Component') {
            $this->warn('You have to select a Netelement of Type Passive Component');

            return 1;
        }

        if ($this->argument('url')) {
            $this->parseURL();

            return;
        }

        if ($this->option('modems')) {
            $this->parseString();

            return;
        }

        $this->error('No valid data provided! You need to enter either a valid URL or a NetElement AND at least one Modem!');
    }

    protected function parseUrl()
    {
        $coords = explode('/', $this->argument('url'));
        $modems = collect((new CustomerTopoController())->show_poly(array_pop($coords), true));

        $this->updateModems($modems);
    }

    protected function parseString()
    {
        $modems = explode('+', $this->option('modems'));

        if ($modems[0] == 0) {
            return $this->updateModems(collect(array_slice($modems, 1)));
        }

        return $this->updateModems(collect($modems));
    }

    protected function updateModems($modems)
    {
        Modem::findOrFail($modems)->each->update([
            'next_passive_id' => $this->argument('netelement'),
        ]);

        $this->info("success! {$modems->count()} Modems added.");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['netelement', InputArgument::REQUIRED, 'Id of the Netelement'],
            ['url', InputArgument::OPTIONAL, 'Paste in the URL of TOPO with coordinates'],
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
            ['modems', 'm', InputOption::VALUE_OPTIONAL, 'Ids of the modems connected with +', null],
        ];
    }
}
