<?php

namespace Modules\ProvBase\Observers;

use Module;
use Illuminate\Support\Facades\Log;
use Modules\ProvBase\Entities\Modem;

/**
 * Modem Observer Class
 * Handles changes on CMs, can handle:
 *
 * 'creating', 'created', 'updating', 'updated',
 * 'deleting', 'deleted', 'saving', 'saved',
 * 'restoring', 'restored',
 */
class ModemObserver
{
    public function created($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        if (Module::collections()->has('PropertyManagement')) {
            $modem->updateAddressFromProperty();
        }

        $hostname = ($modem->isPPP() ? 'ppp-' : 'cm-').$modem->id;
        $modem->hostname = $hostname;

        // always set hostname, even if updating() fails (e.g php warning)
        // this is needed for a consistent dhcpd config
        Modem::where('id', $modem->id)->update(['hostname' => $hostname]);

        $modem->updateRadius();

        $modem->save();  // forces to call the updating() and updated() method of the observer !

        if (Module::collections()->has('ProvMon')) {
            Log::info("Create cacti diagrams for modem: $modem->hostname");
            \Artisan::call('nms:cacti', ['--netgw-id' => 0, '--modem-id' => $modem->id]);
        }

        if (! $modem->network_access) {
            Modem::createDhcpBlockedCpesFile();
        }
    }

    public function updating($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        // reminder: on active envia TEL module: moving modem to other contract is not allowed!
        // check if this is running if you decide to implement moving of modems to other contracts
        // watch Ticket LAR-106
        if (Module::collections()->has('ProvVoipEnvia')) {
            // updating is also called on create – so we have to check this
            if ((! $modem->wasRecentlyCreated) && ($modem->isDirty('contract_id'))) {
                // returning false should cancel the updating: verify this! There has been some problems with deleting modems – we had to put the logic in Modem::delete() probably caused by our Base* classes…
                // see: http://laravel-tricks.com/tricks/cancelling-a-model-save-update-delete-through-events
                return false;
            }
        }

        if (! $modem->observer_enabled) {
            return;
        }

        // get changed values
        $diff = $modem->getDirty();

        // if testing: do not try to geocode or position modems (faked data; slows down the process)
        if (\App::runningUnitTests()) {
            return;
        }

        // Use Updating to set the geopos before a save() is called.
        // Notice: that we can not call save() in update(). This will re-trigger
        //         the Observer and re-call update() -> endless loop is the result.
        if ($modem->wasRecentlyCreated && $modem->x && $modem->y && $modem->geocode_source) {
            // do nothing
        } elseif (multi_array_key_exists(['street', 'house_number', 'zip', 'city'], $diff)) {
            $modem->geocode(false);
        } elseif (multi_array_key_exists(['x', 'y'], $diff) && ! \App::runningInConsole()) {
            // Manually changed geodata
            // Change geocode_source only from MVC (and do not overwrite data from geocode command)
            $user = \Auth::user();
            $modem->geocode_source = $user->first_name.' '.$user->last_name;
        }

        // check if more values have changed – especially “x” and “y” which refreshes MPR
        $diff = $modem->getDirty();

        // Refresh MPS rules
        // Note: does not perform a save() which could trigger observer.
        if (Module::collections()->has('HfcCustomer')) {
            if (multi_array_key_exists(['x', 'y'], $diff)) {
                // suppress output in this case
                ob_start();
                \Modules\HfcCustomer\Entities\Mpr::ruleMatching($modem);
                ob_end_clean();
            }
        }
    }

    public function updated($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        if (! $modem->observer_enabled) {
            return;
        }

        // Only restart, make dhcp and configfile and only restart dhcpd via systemdobserver when it's necessary
        $diff = $modem->getDirty();
        if (multi_array_key_exists(['contract_id', 'public', 'internet_access', 'configfile_id', 'qos_id', 'mac', 'serial_num'], $diff)) {
            Modem::create_ignore_cpe_dhcp_file();
            $modem->make_dhcp_cm();

            if (array_key_exists('internet_access', $diff)) {
                Modem::createDhcpBlockedCpesFile();
            }

            if (! $modem->wasRecentlyCreated) {
                $modem->restart_modem(array_key_exists('mac', $diff));
            }

            $modem->make_configfile();
        }

        $modem->updateRadius();

        // ATTENTION:
        // If we ever think about moving modems to other contracts we have to delete envia TEL related stuff, too –
        // check contract_ext* and installation_address_change_date
        // moving then should only be allowed without attached phonenumbers and terminated envia TEL contract!
        // cleaner in Patrick's opinion would be to delete and re-create the modem

        if (array_key_exists('apartment_id', $diff)) {
            $modem->updateAddressFromProperty();
        }
    }

    public function deleted($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        if ($modem->isTR069()) {
            $modem->deleteGenieAcsProvision();
            $modem->deleteGenieAcsPreset();
            $modem->factoryReset();
        }

        $modem->updateRadius();

        Modem::create_ignore_cpe_dhcp_file();
        if (! $modem->network_access) {
            Modem::createDhcpBlockedCpesFile();
        }
        $modem->make_dhcp_cm(true);
        $modem->restart_modem();
        $modem->delete_configfile();
    }
}
