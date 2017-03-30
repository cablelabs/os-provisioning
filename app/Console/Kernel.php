<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \Modules\HfcBase\Http\Controllers\TreeErdController;
use \Modules\HfcBase\Http\Controllers\TreeTopographyController;
use \Modules\HfcCustomer\Http\Controllers\CustomerTopoController;
use \Modules\ProvVoip\Console\CarrierCodeDatabaseUpdaterCommand;
use \Modules\ProvVoip\Console\EkpCodeDatabaseUpdaterCommand;
use \Modules\ProvVoip\Console\TRCClassDatabaseUpdaterCommand;
use \Modules\ProvVoip\Console\PhonenumberCommand;
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
		'App\Console\Commands\StorageCleaner',
		'\Modules\ProvVoip\Console\CarrierCodeDatabaseUpdaterCommand',
		'\Modules\ProvVoip\Console\EkpCodeDatabaseUpdaterCommand',
		'\Modules\ProvVoip\Console\TRCClassDatabaseUpdaterCommand',
		'\Modules\ProvVoip\Console\PhonenumberCommand',
		'\Modules\ProvVoipEnvia\Console\EnviaOrderUpdaterCommand',
		'\Modules\ProvVoipEnvia\Console\VoiceDataUpdaterCommand',
		'\Modules\ProvVoipEnvia\Console\EnviaOrderProcessorCommand',
		'App\Console\Commands\authCommand',
		'App\Console\Commands\install',
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


		// Remove all Log Entries older than 90 days
		$schedule->call('\App\GuiLog@cleanup')->weekly();

		// Command to remove obsolete data in storage
		$schedule->command('main:storage_cleaner')->dailyAt('04:18');

		if (\PPModule::is_active ('ProvVoip')) {

			// Update database table carriercode with csv data if necessary
			$schedule->command('provvoip:update_carrier_code_database')
				->dailyAt('03:24');

			// Update database table ekpcode with csv data if necessary
			$schedule->command('provvoip:update_ekp_code_database')
				->dailyAt('03:29');

			// Update database table trcclass with csv data if necessary
			$schedule->command('provvoip:update_trc_class_database')
				->dailyAt('03:34');
		}

		if (\PPModule::is_active ('ProvVoipEnvia')) {

			// Update status of envia orders
			// Do this at the very beginning of a day
			$schedule->command('provvoipenvia:update_envia_orders')
				->dailyAt('00:01');
				/* ->everyMinute(); */

			// Update voice data
			$schedule->command('provvoipenvia:update_voice_data')
				->dailyAt('03:53');
				/* ->everyMinute(); */

			// Process Envia orders
			$schedule->command('provvoipenvia:process_envia_orders')
				->dailyAt('00:23');
		}

		// ProvBase Schedules
		if (\PPModule::is_active ('ProvBase'))
		{
			// Rebuid all Configfiles
			// $schedule->command('nms:configfile')->dailyAt('00:50')->withoutOverlapping();

			// TODO: Reload DHCP on clock change (daylight saving) - last sunday in march, last sunday in october
			// [0] minute, [1] hour, [2] day, [3] month, [4] day of week, [5] year
			$schedule->command('nms:dhcp')->cron('0 4 24,25,26,27,28,29,30,31 3,10 0 *');

			// Contract - network access, item dates, internet (qos) & voip tariff changes
			// important!! daily conversion has to be run BEFORE monthly conversion
			// commands within one call of “artisan schedule:run” should be processed sequentially (AFAIR)
			// but to force the order we add runtimes: ten minutes difference should be more than enough
			// TODO: ckeck if this is really needed
			$schedule->command('nms:contract daily')->daily()->at('00:03');
			$schedule->command('nms:contract monthly')->monthly()->at('00:13');
		}

		// Clean Up of HFC Base
		if (\PPModule::is_active ('HfcBase'))
		{
			// Rebuid all Configfiles
			$schedule->call(function () {
				\Storage::deleteDirectory(TreeTopographyController::$path_rel);
				\Storage::deleteDirectory(TreeErdController::$path_rel);
			})->hourly();
		}

		// Clean Up of HFC Customer
		if (\PPModule::is_active ('HfcCustomer'))
		{
			// Rebuid all Configfiles
			$schedule->call(function () {
				\Storage::deleteDirectory(CustomerTopoController::$path_rel);
			})->hourly();

			// Modem Positioning System
			$schedule->command('nms:mps')->daily();

			$schedule->command('nms:modem-refresh')->everyFiveMinutes()->withoutOverlapping();
		}


		// Automatic Power Control based on measured SNR
		if (\PPModule::is_active ('ProvMon'))
		{
			$schedule->command('nms:apc')->everyFiveMinutes()->withoutOverlapping();
			$schedule->command('nms:cacti')->daily();
		}


		// TODO: improve
		$schedule->call(function () {
				exec ('chown -R apache '.storage_path('logs'));
			})->dailyAt('00:01');


		// Create monthly Billing Files and reset flags
		if (\PPModule::is_active ('BillingBase'))
		{
			// wrapping into a check if table billingbase exists (if not that crashes on every “php artisan” command – e.g. on migrations
			if (\Schema::hasTable('billingbase')) {
				$schedule->call('\Modules\BillingBase\Entities\Item@yearly_conversion')->yearly();

				// $rcd = \Modules\BillingBase\Entities\BillingBase::select('rcd')->first()->rcd;
				// $execute = $rcd ? ($rcd - 5 > 0 ? $rcd - 5 : 1) : 15;
				// This does not consider CDRs because .env file is not read properly in super global var by executing as cron job - adapt cdrCommand!
				// $schedule->command('nms:accounting')->monthlyOn($execute, '01:00');
				// TODO: create SettlementRun here!
			}
		}

		if (\PPModule::is_active('ProvVoip')) {
			$schedule->command('provvoip:phonenumber')->daily()->at('00:13');
		}

		if (\PPModule::is_active ('VoipMon'))
		{
			$schedule->command('voipmon:match_records')->everyFiveMinutes();
			$schedule->command('voipmon:delete_old_records')->daily();
		}
	}

}
