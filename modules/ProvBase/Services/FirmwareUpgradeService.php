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

        return FirmwareUpgrades::where(function ($query) use ($nowDate, $nowTime) {
            $query->where('start_date', '<', $nowDate)
                ->orWhere(function ($query) use ($nowDate, $nowTime) {
                    $query->where('start_date', $nowDate)
                        ->where('start_time', '<=', $nowTime);
                });
            })
            ->whereNull('finished_date')
            ->get();
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

            // If no modems to update, set finished_date and continue to next upgrade
            if ($modems->isEmpty()) {
                Log::info('Firmware upgrade process has finished for upgrade id: '.$firmwareUpgrade->id);
                $firmwareUpgrade->update(['finished_date' => Carbon::now()]);
                continue;
            }

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
