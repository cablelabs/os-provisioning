<?php

namespace Modules\ProvBase\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\ProvBase\Entities\ProvBase;

class HardwareSupportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:hardware-support';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checks for hardware support of each modem and CMTS';

    /**
     * Execute the console command - Check for hardware support for each modem and each CMTS modules
     *
     * @return mixed
     */
    public function handle()
    {
        if (! \App\Sla::first()->valid()) {
            return;
        }

        $provBaseSettings = ProvBase::first();

        $this->snmp_def_mode();
        $modems = DB::table('modem')->whereNull('deleted_at')->get();
        $cmtses = DB::table('netgw')->whereNull('deleted_at')->where('type', 'cmts')->get();
        $ro_community = $provBaseSettings->ro_community;

        foreach ($modems as $modem) {
            $hostname = "{$modem->hostname}.{$provBaseSettings->domain_name}";
            $support_state = 'not-supported';
            if (! isset($modem->serial_num) || $modem->serial_num === '') {
                try {
                    $modem->serial_num = snmpget($hostname, $ro_community, '1.3.6.1.2.1.69.1.1.4.0');
                } catch (\Exception $exception) {
                    $this->error("Modem: {$modem->hostname}, snmp Exception: ".$exception->getMessage());
                }
            }
            $modem_serial_no_md5 = md5($modem->serial_num);
            $contents = file_get_contents('https://support.nmsprime.com/hwsn/api.php?q='.$modem_serial_no_md5);

            if ($contents !== '') {
                $result = json_decode($contents, true);
                if (isset($result[$modem_serial_no_md5]) && $result[$modem_serial_no_md5] === 'valid') {
                    $support_state = 'full-supported';
                } elseif ((Carbon::parse($modem->created_at))->diffInWeeks(Carbon::now()) < 6) {
                    $support_state = 'verifying';
                }
            }

            DB::table('modem')->where('id', $modem->id)->update(['serial_num' => $modem->serial_num, 'support_state' => $support_state, 'updated_at' => (Carbon::now())->toDateTimeString()]);
        }

        foreach ($cmtses as $cmts) {
            $hostname = $cmts->ip;
            $support_state = 'not-supported';

            try {
                $cmts_serials = snmpwalk($hostname, $ro_community, '1.3.6.1.2.1.47.1.1.1.1.11');
                $cmts_serials = array_filter($cmts_serials, 'strlen');

                $count_found = 0;
                foreach ($cmts_serials as $cmts_serial) {
                    $cmts_serial_md5 = md5($cmts_serial);
                    $contents = file_get_contents('https://support.nmsprime.com/hwsn/api.php?q='.$cmts_serial_md5);

                    if ($contents !== '') {
                        $result = json_decode($contents, true);
                        if (isset($result[$cmts_serial_md5]) && $result[$cmts_serial_md5] === 'valid') {
                            $count_found++;
                        }
                    }
                }

                if ($count_found) {
                    $percentage = $count_found / count($cmts_serials) * 100;

                    if ($percentage > 95) {
                        $support_state = 'full-supported';
                    } elseif ($percentage > 80 && $percentage <= 95) {
                        $support_state = 'restricted';
                    } elseif ((Carbon::parse($cmts->created_at))->diffInWeeks(Carbon::now()) < 6) {
                        $support_state = 'verifying';
                    }
                }
                $this->info(sprintf('CMTS %s is %s%% supported', $cmts->hostname, $percentage));
                DB::table('cmts')->where('id', $cmts->id)->update(['support_state' => $support_state, 'updated_at' => (Carbon::now())->toDateTimeString()]);
            } catch (\Exception $exception) {
                $this->error("CMTS: {$hostname}, error message: ".$exception->getMessage());
            }
        }
    }

    /**
     * Set PHP SNMP Default Values
     * Note: Must be only called once per Object Init
     *
     * Note: copied from SnmpController
     */
    private function snmp_def_mode()
    {
        snmp_set_quick_print(true);
        snmp_set_oid_numeric_print(true);
        snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
    }
}
