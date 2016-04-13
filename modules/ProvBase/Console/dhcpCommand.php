<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Cmts;
use Modules\ProvBase\Entities\ProvBase;
use Modules\ProvVoip\Entities\Mta;

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
		$g = ProvBase::first();
		$g->make_dhcp_glob_conf();

		// Modems
		$cm = Modem::first();
		$cm->del_dhcp_conf_files();
		$cm->make_dhcp_cm_all();

		// Endpoints
		$e = Endpoint::first();
		if (isset($e))
			$e->make_dhcp();

		// Mta's
		$m = Mta::first();
		$m->del_dhcp_conf_file();
		$m->make_dhcp_mta_all();

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
