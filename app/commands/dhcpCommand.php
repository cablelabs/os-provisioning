<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Models\Modem;
use Models\Endpoint;
use Models\Cmts;

class dhcpCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:dhcp';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'make the DHCP config';

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
		// Modems
		$m = Modem::first();
		$m->del_dhcp_conf_files();
		$m->make_dhcp_cm_all();

		// Endpoints
		$e = Endpoint::first();
		$e->make_dhcp();

		// CMTS's
		// remove all through cmts config files
		$files = glob('/etc/dhcp/nms/cmts_gws/*');		// get all files in dir
		foreach ($files as $file) 
		{
			if(is_file($file))
			unlink($file);
		}

		$c = Cmts::all();
		$c->first()->del_cmts_includes();

		foreach ($c as $cmts) 
		{
			$cmts->make_dhcp_conf();
		}
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
