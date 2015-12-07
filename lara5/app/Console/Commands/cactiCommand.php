<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Models\Modem;

class cactiCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:cacti';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create cablemodem diagrams';

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
		$cms = Modem::all();

		$i = 1; 
		$num = count ($cms);

		foreach ($cms as $cm)
		{
			//echo "create cacti monitoring : $i/$num \r"; $i++;

			$name      = $cm->hostname;
			$hostname  = $name.'.test.erznet.tv';
			$community = 'public'; # TODO: global config

			$cmd = "/var/www/lara/app/scripts/cacti_add.sh $name $hostname $community";
			echo exec($cmd);
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
