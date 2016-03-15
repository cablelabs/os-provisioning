<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \Modules\ProvVoip\Console\CarrierCodeDatabaseUpdaterCommand;
use \Modules\ProvVoipEnvia\Console\EnviaOrderUpdaterCommand;

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
		'\Modules\ProvVoipEnvia\Console\EnviaOrderUpdaterCommand',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		/* $schedule->command('inspire') */
		/* 		 ->hourly(); */

		// comment the following in to see the time shifting behaviour of the scheduler;
		// watch App\Console\Commands\TimeDeltaChecker for more informations
		/* $schedule->command('main:time_delta') */
			/* ->everyMinute(); */


		// Update database table carriercode with csv data if necessary
		$schedule->command('provvoip:update_carrier_code_database')
			->dailyAt('03:24');

		// Update status of envia orders
		$schedule->command('provvoipenvia:update_envia_orders')
			->dailyAt('03:37');
			/* ->everyMinute(); */
	}

}
