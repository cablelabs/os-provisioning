<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\ProvBase\Entities\Modem;

class configfileCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:configfile';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'make alle configfiles';

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
		// Modem
		$cms = Modem::all();

		$i = 1; 
		$num = count ($cms);

		foreach ($cms as $cm)
		{
			echo "CM: create config files: $i/$num \r"; $i++;
			$cm->make_configfile();
		}
		echo "\n";


		// MTA
		$mtas = \Modules\ProvVoip\Entities\Mta::all();

		$i = 1; 
		$num = count ($mtas);

		foreach ($mtas as $mta)
		{
			echo "MTA: create config files: $i/$num \r"; $i++;
			$mta->make_configfile();
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
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
