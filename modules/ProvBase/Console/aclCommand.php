<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\ProvBase\Entities\Cmts;

class aclCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:acl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generates a bundle ACL from ippools';

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
		foreach ($this->option('cmts-id') ? [Cmts::findOrFail($this->option('cmts-id'))] : Cmts::all() as $cmts) {
			echo "$cmts->hostname:\n";
			echo "ip access-list extended bundle_in_acl\n";
			echo "  remark Bundle-in-ACL\n";
			// deny access to CMs
			foreach($cmts->ippools()->where('type', '=', 'CM')->get() as $cm_pool)
				echo "  permit ip any host $cm_pool->router_ip\n";
			echo "  permit ip any 10.0.254.0 0.0.0.127\n";
			echo "  deny   ip any 10.0.0.0 0.255.255.255\n";
			// deny access to private CPEs
			echo "  deny   ip any 100.64.0.0 0.63.255.255\n";
			// deny access to MTAs
			foreach($cmts->ippools()->where('type', '=', 'MTA')->get() as $mta_pool) {
				echo "  deny   ip any host $mta_pool->net ";
				$mask = [];
				foreach(explode('.', $mta_pool->netmask) as $val)
					$mask[] = ~intval($val) & 255;
				echo implode('.', $mask)."\n";
			}
			// permit everything else
			echo "  permit ip any any\n";
			echo "access-list compiled\n\n";
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
			array('cmts-id', null, InputOption::VALUE_OPTIONAL, 'only consider cmts identified by its id, otherwise all', null),
		);
	}

}
