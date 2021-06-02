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
