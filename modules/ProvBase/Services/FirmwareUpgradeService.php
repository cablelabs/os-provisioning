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

    /**
     * Execute firmware upgrades on modems.
     *
     * This method iterates over active firmware upgrades and retrieves
     * the modems that match the conditions for each upgrade. If an upgrade
     * does not require restart only, it updates the modems with a new configfile_id.
     * Modems restart automatically after being updated. The process is then logged.
     *
     * @return void
     */
    public function upgradeFirmware()
    {
        $activeFirmwareUpgrades = $this->getActiveFirmwareUpgrades();

        foreach ($activeFirmwareUpgrades as $firmwareUpgrade) {
            $modems = $this->getMatchingModems($firmwareUpgrade);

            // If no modems to update, set finished_date and continue to next upgrade
            if ($modems->isEmpty()) {
                Log::info('Firmware upgrade process has finished for upgrade id: '.$firmwareUpgrade->id);
                $firmwareUpgrade->update(['finished_date' => Carbon::now()]);
                continue;
            }

            // If restart_only is false, update the Modems to use to_configfile_id
            if (!$firmwareUpgrade->restart_only) {
                $this->updateModemConfigfile($modems, $firmwareUpgrade->to_configfile_id);
                // Return early since modems restart automatically after being updated
                return;
            }

            // Manually restarting modems if restart_only is true
            foreach ($modems as $modem) {
                $modem->restart_modem();
            }
        }
    }

    /**
     * Updates the configfile_id for a collection of Modems.
     *
     * @param  Collection $modems The modems to update.
     * @param  int $configfileId The ID of the new configfile.
     * @return void
     */
    protected function updateModemConfigfile(Collection $modems, int $configfileId)
    {
        DB::beginTransaction();

        foreach ($modems as $modem) {
            $modem->configfile_id = $configfileId;
            $modem->save();
        }

        DB::commit();

        // Log the number of updated modems
        Log::info(count($modems) . ' modems have been updated with configfile id: ' . $configfileId);
    }

    /**
     * Retrieves a collection of Modem models that match the criteria specified in the provided firmware upgrade.
     *
     * This method will filter modems based on the `configfile_id`s associated with the firmware upgrade. If the
     * firmware upgrade specifies a `restart_only` flag, the method will also filter modems whose `sw_rev` matches
     * any of the lines in the `firmware_match_string` of the firmware upgrade.
     *
     * If a `batch_size` is specified in the firmware upgrade, the method will limit the number of modems returned to
     * that batch size.
     *
     * @param FirmwareUpgrade $firmwareUpgrade The firmware upgrade which specifies the criteria for matching modems.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of Modem models that match the criteria.
     */
    protected function getMatchingModems($firmwareUpgrade)
    {
        $configfileIds = $firmwareUpgrade->fromConfigfile()->pluck('configfile_id');
        $modems = Modem::whereIn('configfile_id', $configfileIds);

        if ($firmwareUpgrade->restart_only) {
            // Split the firmware_match_string into an array of lines
            $matchStrings = preg_split('/\r\n|\r|\n/', $firmwareUpgrade->firmware_match_string);

            $modems = $modems->where(function ($query) use ($matchStrings) {
                foreach ($matchStrings as $matchString) {
                    $query = $query->orWhere('sw_rev', 'LIKE', "%$matchString%");
                }
            });
        }

        // Limit to batch size if it's specified
        if (! empty($firmwareUpgrade->batch_size)) {
            $modems = $modems->limit($firmwareUpgrade->batch_size);
        }

        return $modems->get();
    }
}
