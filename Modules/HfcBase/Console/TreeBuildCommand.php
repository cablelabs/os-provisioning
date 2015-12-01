<?php

namespace Modules\hfcbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\HfcBase\Entities\Tree;

use Modules\HfcCustomer\Entities\ModemHelper;

class TreeBuildCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:tree';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'HfcBase: Tree - build net and cluster index';

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
		if ($this->option('debug'))
			dd("debug");

		Tree::relation_index_build_all(2);

		return;
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
			array('debug', 'd', InputOption::VALUE_OPTIONAL, 'Debug Net and Cluster Outputs', 0),
		);
	}

}
