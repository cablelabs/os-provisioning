<?php namespace Modules\Provvoip\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CarrierCodeDatabaseUpdaterCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'provvoip:update_carrier_code_database';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update the database carrier code using the csv file $csv_file';

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
		// dummy content to check if laravel's cron is configured correctly
		$content = date("c");
		$content .= "\n";
		$content .= shell_exec('date --iso-8601=seconds');
		file_put_contents('/tmp/laravel_comnand_tester.par', $content);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		/* return [ */
		/* 	['example', InputArgument::REQUIRED, 'An example argument.'], */
		/* ]; */
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		/* return [ */
		/* 	['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null], */
		/* ]; */
		return [];
	}

}
