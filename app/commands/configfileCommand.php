<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Models\Modem;

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
		// remove all config files
		$files = glob('/tftpboot/cm/*');              // get all files in dir
		foreach ($files as $file) 
		{
			if(is_file($file))
			unlink($file);
		}

		$cms = Modem::all();

		$i = 1; 
		$num = count ($cms);

		foreach ($cms as $cm)
		{
			echo "create config files: $i/$num \r"; $i++;
			$cm->make_configfile();
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
