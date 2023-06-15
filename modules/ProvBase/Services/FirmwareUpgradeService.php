<?php

namespace Modules\ProvBase\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ProvBase\Entities\FirmwareUpgrades;
use Modules\ProvBase\Entities\Modem;

class FirmwareUpgradeService
{
    public function getActiveFirmwareUpgrades(): \Illuminate\Support\Collection
    {
        // Get all Firmware upgrades within active period
        $now = Carbon::now();
        $nowDate = $now->toDateString();
        $nowTime = $now->format('H:i');

        $firmwareUpgrades = FirmwareUpgrades::where(function ($query) use ($nowDate, $nowTime) {
            $query->where('start_date', '<', $nowDate)
                ->orWhere(function ($query) use ($nowDate, $nowTime) {
                    $query->where('start_date', $nowDate)
                        ->where('start_time', '<=', $nowTime);
                });
        })->whereNotNull('finished_date');

        $results = $firmwareUpgrades->get();

        if ($results->isEmpty()) {
            $firmwareUpgrades->update(['finished_date' => Carbon::now()]);
        }

        return $results;
    }

    public function upgradeFirmware()
    {
        $activeFirmwareUpgrades = $this->getActiveFirmwareUpgrades();

        foreach ($activeFirmwareUpgrades as $firmwareUpgrade) {
            // Get Modems that has the configfile_id in the fromConfigfile relationship
            $configfileIds = $firmwareUpgrade->fromConfigfile()->pluck('configfile_id');
            $modems = Modem::whereIn('configfile_id', $configfileIds);

            // Limit to batch size if it's specified
            if (! empty($firmwareUpgrade->batch_size)) {
                $modems = $modems->limit($firmwareUpgrade->batch_size);
            }

            $modems = $modems->get();
            $count = 0;
            DB::beginTransaction();

            // Update the Modems to use to_configfile_id
            foreach ($modems as $modem) {
                $modem->configfile_id = $firmwareUpgrade->to_configfile_id;
                $modem->save();
                $modem->restart_modem();
                $count++;
            }

            DB::commit();

            if ($count > 0) {
                Log::info('Firmware upgrade process has been done successfully for '.$count.' modems.');
            }
        }
    }
}
