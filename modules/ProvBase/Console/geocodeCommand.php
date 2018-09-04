<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Modem;

class geocodeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:geocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find geocodes (x,y) for all modems. Accepts number of modems to process as argument (default 250). Add “--debug” for verbose output.';

    /**
     * The signature (defining the optional argument)
     */
    protected $signature = 'nms:geocode
							{modem_count=250 : Number of modems to geocode (limited because of usage policies)} {--debug}';

    // helpers filled from CLI args/opts
    protected $modem_count = 250;
    protected $debug = false;

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
     * This method takes care for usage policies of OSM/google (meaning that this has not to be handled in Modem::geocode()
     *
     * @return mixed
     * @author Torsten Schmidt, Patrick Reichel
     */
    public function fire()
    {
        $this->getParams();

        // get all modems with missing or uncertain geodata
        // ordering puts modems with uncertain data first (correct wrong entries)
        // second are all modems where geocode failed before
        $modems = Modem::whereNull('geocode_source')
            ->orWhere('geocode_source', '=', '')
            ->orWhere('geocode_source', 'like', 'n/a%')
            ->orderBy('geocode_source')->orderBy('id')
            ->take($this->modem_count)
            ->get();

        $this->info('Trying to geocode '.$modems->count().' modems');

        $bar = $this->output->createProgressBar($modems->count());

        foreach ($modems as $modem) {
            ob_start();
            $ret = $modem->geocode(true);
            ob_end_clean();

            if ($this->debug) {
                $info = 'id:'.$modem->id.', '.$modem->zip.', '.$modem->city.', '.$modem->street.' '.$modem->house_number;

                if ($ret) {
                    $this->info(implode(', ', $ret).', '.$info);
                } else {
                    $this->error($modem->geocode_last_status().': error, could not translate, '.$info.' - ');
                }
            } else {
                $bar->advance();
            }

            // sleep between 1.2 and 1.5 seconds (Nominatim allows one request per second, google much more)
            $sleeptime = rand(1200000, 1500000);
            usleep($sleeptime);
        }

        echo "\n";
    }

    /**
     * Get the console command arguments and options.
     *
     * @author Patrick Reichel
     */
    protected function getParams()
    {

        // get number of modems to geocode
        $modem_count = $this->argument('modem_count');

        if (! is_numeric($modem_count) || (intval($modem_count) < 1)) {
            echo 'Error: Pass integer >0 as argument';
            exit(1);
        }
        $this->modem_count = intval($modem_count);

        if ($this->option('debug')) {
            $this->debug = true;
        }
    }
}
