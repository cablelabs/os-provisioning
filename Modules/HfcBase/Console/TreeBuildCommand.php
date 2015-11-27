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
		$s = 'id > 0';
		echo "\r\n ms_num     = ".ModemHelper::ms_num($s);
		echo "\r\n ms_num_all = ".ModemHelper::ms_num_all($s);
		echo "\r\n ms_avg     = ".ModemHelper::ms_avg($s);
		echo "\r\n ms_cri     = ".ModemHelper::ms_cri($s);
		echo "\r\n ms_state   = ".ModemHelper::ms_state($s);


		return;

		foreach (Tree::where('id', '>', '2')->get() as $tree) 
		{
	        $tree->net   = $tree->get_native_net();
	        $tree->cluster = $tree->get_native_fibre();
	        $tree->save();

	        echo "\r\n".$tree->id.' '.$tree->net.' '.$tree->cluster;
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
