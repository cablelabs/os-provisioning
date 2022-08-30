<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Console;

use Queue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * NOTE: the withoutOverlapping() statement is just for security reasons
     * and should never be required. But if a task hangs up, this will
     * avoid starting many parallel tasks. (Torsten Schmidt)
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // if provha is disabled: run main commands
        if (! \Module::collections()->has('ProvHA')) {
            $this->scheduleMain($schedule);

            return;
        }

        // if master: run main and additional master commands
        if ('master' == config('provha.hostinfo.ownState')) {
            $this->scheduleMain($schedule);
            $this->scheduleMaster($schedule);

            return;
        }

        // if slave: run slave commands only
        if ('slave' == config('provha.hostinfo.ownState')) {
            $this->scheduleSlave($schedule);

            return;
        }

        // do nothing at unclear states
    }

    /**
     * Run scheduled commands for single and master instances.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function scheduleMain(Schedule $schedule)
    {
        /* $schedule->command('inspire') */
        /* 		 ->hourly(); */

        // comment the following in to see the time shifting behaviour of the scheduler;
        // watch App\Console\Commands\TimeDeltaChecker for more informations
        /* $schedule->command('main:time_delta') */
        /* ->everyMinute(); */

        // define some helpers
        $is_first_day_of_month = (date('d') == '01') ? true : false;

        // calculate an offset that can be used to time-shift cron commands
        // (e.g. to distribute load on external APIs)
        // use like: ->dailyAt(date("H:i", strtotime("04:04 + $time_offset min")));
        $key = getenv('APP_KEY') ?: 'n/a';
        $hash = sha1($key);
        $subhash = substr($hash, -2);   // [00..ff] => [0..255]
        $time_offset = hexdec($subhash) % 32;    // offset in [0..31] minutes

        // Remove all Log Entries older than 4 years
        $schedule->call('\App\GuiLog@cleanup')->weekly();

        if (\Module::collections()->has('CoreMon')) {
            // Remove all alarms older than 2 years
            $schedule->call('\Modules\CoreMon\Entities\Alarm@cleanup')->weekly();

            // import data from Kafka every 15 minutes
            $schedule->call(function () {
                \Queue::pushOn('low', new \Modules\CoreMon\Jobs\ImportKafkaArpDataJob());
            })->cron('5,20,35,50 * * * *');
        }
        // Parse News from repo server and save to local JSON file
        if (\Module::collections()->has('Dashboard')) {
            $schedule->call('\Modules\Dashboard\Http\Controllers\DashboardController@newsLoadToFile')->hourly();
        }

        if (config('datatables.isIndexCachingEnabled')) {
            $schedule->call(function () {
                \App\Jobs\CacheIndexTableCountJob::dispatch();
            })->dailyAt('03:55');
        }

        // Command to remove obsolete data in storage
        $schedule->command('main:storage_cleaner')->dailyAt('04:18');

        if (\Module::collections()->has('ProvVoip')) {

            // Update database table carriercode with csv data if necessary
            $schedule->command('provvoip:update_carrier_code_database')
                ->dailyAt('04:23');

            // Update database table ekpcode with csv data if necessary
            $schedule->command('provvoip:update_ekp_code_database')
                ->dailyAt('04:28');

            // Update database table trcclass with csv data if necessary
            $schedule->command('provvoip:update_trc_class_database')
                ->dailyAt('04:33');
        }

        if (\Module::collections()->has('ProvVoipEnvia')) {

            // Update status of envia orders
            // Do this at the very beginning of a day
            $schedule->command('provvoipenvia:update_envia_orders')
                ->dailyAt(date('H:i', strtotime("04:04 + $time_offset min")));
            /* ->everyMinute(); */

            // Get envia TEL customer reference for contracts without this information
            $schedule->command('provvoipenvia:get_envia_customer_references')
                ->dailyAt(date('H:i', strtotime("01:13 + $time_offset min")));

            // Get/update envia TEL contracts
            $schedule->command('provvoipenvia:get_envia_contracts_by_customer')
                ->dailyAt(date('H:i', strtotime("01:18 + $time_offset min")));

            // Process envia TEL orders (do so after getting envia contracts)
            $schedule->command('provvoipenvia:process_envia_orders')
                ->dailyAt(date('H:i', strtotime("03:18 + $time_offset min")));

            // Get envia TEL contract reference for phonenumbers without this information or inactive linked envia contract
            // on first of a month: run in complete mode
            // do so after provvoipenvia:process_envia_orders as we need the old references there
            if ($is_first_day_of_month) {
                $tmp_cmd = 'provvoipenvia:get_envia_contract_references complete';
            } else {
                $tmp_cmd = 'provvoipenvia:get_envia_contract_references';
            }
            $schedule->command($tmp_cmd)
                ->dailyAt(date('H:i', strtotime("03:23 + $time_offset min")));

            // Update voice data
            // on first of a month: run in complete mode
            if ($is_first_day_of_month) {
                $tmp_cmd = 'provvoipenvia:update_voice_data complete';
            } else {
                $tmp_cmd = 'provvoipenvia:update_voice_data';
            }
            $schedule->command($tmp_cmd)
                ->dailyAt(date('H:i', strtotime("01:23 + $time_offset min")));
        }

        // ProvBase Schedules
        if (\Module::collections()->has('ProvBase')) {
            // Rebuid all Configfiles
            // $schedule->command('nms:configfile')->dailyAt('00:50')->withoutOverlapping();

            // Reload DHCP on clock change (daylight saving)
            // [0] minute, [1] hour, [2] day, [3] month, [4] day of week, [5] year
            $day1 = date('d', strtotime('last sunday of march'));
            $day2 = date('d', strtotime('last sunday of oct'));
            $schedule->call(function () {
                Queue::pushOn('high', new \Modules\ProvBase\Jobs\DhcpJob());
            })->cron("0 4 $day1 3 0");
            $schedule->call(function () {
                Queue::pushOn('high', new \Modules\ProvBase\Jobs\DhcpJob());
            })->cron("0 4 $day2 10 0");

            // Contract - network access, item dates, internet (qos) & voip tariff changes
            // important!! daily conversion must run BEFORE monthly conversion
            // jobs on same queue should be processed sequentially (AFAIR) - but to force the order we add runtimes
            $schedule->call(function () {
                Queue::pushOn('low', new \Modules\ProvBase\Jobs\ContractJob('daily'));
            })->daily()->at('00:03');
            $schedule->call(function () {
                Queue::pushOn('low', new \Modules\ProvBase\Jobs\ContractJob('monthly'));
            })->monthly()->at('00:33');

            $schedule->call(function () {
                Queue::pushOn('low', new \Modules\ProvBase\Jobs\DeleteStaleGenieAcsTasksJob());
            })->daily()->at('01:03');

            $schedule->call(function () {
                foreach (\Modules\ProvBase\Entities\NetGw::where('type', 'olt')->where('ssh_auto_prov', '1')->get() as $olt) {
                    $olt->runSshAutoProv();
                }
            })->everyFiveMinutes();

            // Hardware support check for modems and CMTS
            // $schedule->command('nms:hardware-support')->twiceDaily(10, 14);
        }

        // Automatic Power Control based on measured SNR
        if (\Module::collections()->has('HfcReq')) {
            $schedule->command('nms:agc')->everyMinute();
        }

        if (\Module::collections()->has('HfcBase')) {
            $schedule->command('nms:icingadata')->cron('4-59/5 * * * *');

            // Clean Up of HFC Base
            $schedule->call(function () {
                \Storage::deleteDirectory(\Modules\HfcBase\Http\Controllers\TreeErdController::$path_rel);
            })->hourly();
        }

        if (\Module::collections()->has('ProvMon')) {
            $schedule->command('nms:cacti')->daily();
        } else {
            $schedule->call(function () {
                Queue::pushOn('medium', new \Modules\ProvBase\Jobs\SetCableModemsOnlineStatusJob());
            })->everyFiveMinutes();
        }

        // TODO: improve
        $schedule->call(function () {
            exec('chown -R apache '.storage_path('logs'));
        })->dailyAt('00:01');

        // Create monthly Billing Files and reset flags
        if (\Module::collections()->has('BillingBase')) {
            // Remove all old CDRs & Invoices

            $schedule->call('\Modules\BillingBase\Helpers\BillingAnalysis@saveIncomeToJson')->dailyAt('00:07');
            $schedule->call('\Modules\BillingBase\Helpers\BillingAnalysis@saveContractsToJson')->hourly();
            $schedule->call('\Modules\BillingBase\Entities\Invoice@cleanup')->monthly();
            // Reset payed_month column for yearly charged items for january settlementrun (in february)
            $schedule->call(function () {
                \Modules\BillingBase\Entities\Item::where('payed_month', '!=', '0')->update(['payed_month' => '0', 'updated_at' => date('Y-m-d H:i:s')]);
                \Log::info('Reset all items payed_month flag to 0');
            })->cron('10 0 1 2 *');
        }

        if (\Module::collections()->has('ProvVoip')) {
            $schedule->command('provvoip:phonenumber')->daily()->at('00:13');
        }

        if (\Module::collections()->has('VoipMon')) {
            $schedule->command('voipmon:match_records')->everyFiveMinutes();
            $schedule->command('voipmon:delete_old_records')->daily();
        }

        // TODO: run Kernel.php and supervisor queue workers as user 'apache'
        exec('chown -R apache:apache '.storage_path('framework/cache'));
    }

    /**
     * Run scheduled commands on slave instances.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function scheduleSlave(Schedule $schedule)
    {
        $schedule->command('provha:rebuild_slave_config')->everyMinute()->withoutOverlapping();
    }

    /**
     * Run scheduled commands on master instances.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function scheduleMaster(Schedule $schedule)
    {
        $schedule->command('provha:sync_ha_master_files')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');

        $this->load(__DIR__.'/Commands');
    }
}
