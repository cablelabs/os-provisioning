<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Module;

class install extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install dependencies (using yum) and execute install scripts';

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
	 * @author Ole Ernst
	 */
	public function handle()
	{
		// firstly, handle install directive in nms base folder
		$this->_handle_module(base_path('Install'));

		// secondly, handle all enabled modules
		foreach (Module::enabled() as $module)
			$this->_handle_module($module);
	}

	/**
	 * Execute {before,after}_install.sh script and install dependencies.
	 * This function requires user interaction for improved safety.
	 *
	 * @author Ole Ernst
	 *
	 * @param module to install
	 */
	protected function _handle_module($module)
	{
		if (is_string($module))
			$path = $module;
		else
			$path = $module->getPath().'/Install';

		if (file_exists("$path/before_install.sh"))
			if(readline("$module: $path/before_install.sh? [Y/n] ") != 'n')
				system("/usr/bin/bash $path/before_install.sh");

		$cfg = '';
		if (file_exists("$path/config.cfg"))
			$cfg = parse_ini_file("$path/config.cfg", TRUE)['config']['depends'];
		if ($cfg && readline("$module: yum install $cfg? [Y/n] ") != 'n')
			system("/usr/bin/yum install -y $cfg");

		if (file_exists("$path/after_install.sh"))
			if(readline("$module: $path/after_install.sh? [Y/n] ") != 'n')
				system("/usr/bin/bash $path/after_install.sh");
	}
}
