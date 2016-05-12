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
		'\Modules\ProvVoipEnvia\Console\VoiceDataUpdaterCommand',
		'App\Console\Commands\authCommand',
	];


	/**
	 * Define the application's command schedule.
	 *
	 * NOTE: the withoutOverlapping() statement is just for security reasons
	 * and should never be required. But if a task hangs up, this will avoid
	 * starting many parallel tasks. (Torsten Schmidt)
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


		if (\PPModule::is_active ('ProvVoip')) {

			// Update database table carriercode with csv data if necessary
			$schedule->command('provvoip:update_carrier_code_database')
				->dailyAt('03:24');
		}

		if (\PPModule::is_active ('ProvVoipEnvia')) {

			// Update status of envia orders
			$schedule->command('provvoipenvia:update_envia_orders')
				->dailyAt('03:37');
				/* ->everyMinute(); */

			// Update voice data
			$schedule->command('provvoipenvia:update_voice_data')
				->dailyAt('03:53');
				/* ->everyMinute(); */
		}

		// ProvBase Schedules
		if (\PPModule::is_active ('ProvBase'))
		{
			// Rebuid all Configfiles
			$schedule->command('nms:configfile')->hourly()->withoutOverlapping();

			// TODO: Reload DHCP
			$schedule->command('nms:dhcp')->hourly()->withoutOverlapping();

			// Contract - network access, internet (qos) & voip tariff changes
			$schedule->command('nms:contract daily')->daily();
			$schedule->command('nms:contract monthly')->monthly();
		}

		// Clean Up of HFC Base
		if (\PPModule::is_active ('HfcBase'))
		{
			// Rebuid all Configfiles
			$schedule->call(function () {
			    exec ('rm -rf '.public_path().'/modules/hfcbase/kml/*.kml');
			    exec ('rm -rf '.public_path().'/modules/hfcbase/erd/*.*');
			})->hourly();
		}

		// Clean Up of HFC Customer
		if (\PPModule::is_active ('HfcCustomer'))
		{
			// Rebuid all Configfiles
			$schedule->call(function () {
			    exec ('rm -rf '.public_path().'/modules/hfccustomer/kml/*.kml');
			})->hourly();

			// Modem Positioning System
			$schedule->command('nms:mps')->daily();

			$schedule->command('nms:modem-refresh --schedule=1')->everyFiveMinutes()->withoutOverlapping();
		}

		if (\PPModule::is_active ('ProvMon'))
		{
			$schedule->command('nms:cacti')->everyFiveMinutes()->withoutOverlapping();
		}

		// TODO: improve
		$schedule->call(function () {
			    exec ('chown -R apache '.storage_path().'/logs');
			})->dailyAt('00:01');

	}

}
