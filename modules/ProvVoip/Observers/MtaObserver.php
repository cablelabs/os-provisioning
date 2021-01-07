<?php

namespace Modules\ProvVoip\Observers;

/**
 * MTA Observer Class
 * Handles changes on MTAs
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class MtaObserver
{
    public function created($mta)
    {
        $mta->hostname = 'mta-'.$mta->id;
        $mta->save(); 			// forces to call updated method
        $mta->modem->make_dhcp_cm(false, true);
        $mta->modem->restart_modem();
    }

    public function updated($mta)
    {
        $modifications = $mta->getDirty();
        if (isset($modifications['updated_at'])) {
            unset($modifications['updated_at']);
        }

        // only make configuration files when relevant data was changed
        if ($modifications) {
            if (array_key_exists('mac', $modifications)) {
                $mta->make_dhcp_mta();
                $mta->modem->make_configfile();

                // in case mta mac begun with or is changed to 'ff:' the modem dhcp entry has to be changed as well
                $mta->modem->make_dhcp_cm(false, true);
            }

            $mta->make_configfile();
        }

        $mta->restart();
    }

    public function deleted($mta)
    {
        $mta->make_dhcp_mta(true);
        $mta->modem->make_dhcp_cm(false, true);
        $mta->delete_configfile();
        $mta->modem->make_configfile();
        $mta->modem->restart_modem();
    }
}
