<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Cmts;
use Modules\ProvBase\Entities\ProvBase;

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
		// Global Config part
		$m = ProvBase::first();
		$m->make_dhcp_glob_conf();

		// Modems
		$m = Modem::first();
		$m->del_dhcp_conf_files();
		$m->make_dhcp_cm_all();

		// Endpoints
		$e = Endpoint::first();
		$e->make_dhcp();

		// CMTS's
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
