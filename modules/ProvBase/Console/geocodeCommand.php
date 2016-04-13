<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\ProvBase\Entities\Modem;


class geocodeCommand extends Command {

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
	protected $description = 'Find geocodes (x,y) for all modems';

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
	 * Note: We only have to take care of Googles over query limit
	 *       if we run mutliple requests in a row. So the native geocode()
	 *       function does not need to take care of this issue
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$bar = $this->output->createProgressBar(Modem::count());

		// Google over query limit wait counter
		$wait = 0;

		foreach (Modem::all() as $modem)
		{
// Retry on over query limit
retry:
			$ret = $modem->geocode();

			if ($this->option('debug'))
			{
				$info = 'id:'.$modem->id.', '.$modem->zip.', '.$modem->city.', '.$modem->street;

				if ($ret)
					$this->info (implode (', ', $ret).', '.$info);
				else
					$this->error ($modem->geocode_last_status().': error, could not translate, '.$info.' - ');
			}
			else
				$bar->advance();

			// Take care of google over query limit
			if ($modem->geocode_last_status() == 'OVER_QUERY_LIMIT')
			{
				usleep(400*1000); // 400 ms
				$wait++;
				goto retry;
			}

			// Google Standard: 2500 requests per day, 10 per second
			// see: https://developers.google.com/maps/documentation/geocoding/usage-limits
			usleep((100 + $wait*10)*1000); // 100, 110, 120, .. ms, one step more per over query limit
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
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('debug', null, InputOption::VALUE_OPTIONAL, 'show each translation entry', false),
		);
	}

}
