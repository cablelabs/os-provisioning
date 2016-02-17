<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \Modules\ProvVoip\Console\CarrierCodeDatabaseUpdaterCommand;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
		'App\Console\Commands\TimeDeltaChecker',
		'\Modules\ProvVoip\Console\CarrierCodeDatabaseUpdaterCommand',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();

		// comment the following in to see the time shifting behaviour of the scheduler;
		// watch App\Console\Commands\TimeDeltaChecker for more informations
		/* $schedule->command('main:time_delta') */
		/* 		->everyMinute(); */

		$schedule->command('provvoip:update_carrier_code_database')
				/* ->everyMinute(); */
				->dailyAt('03:24');
	}

}
