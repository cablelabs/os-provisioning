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

namespace Modules\ProvBase\Observers;

use Illuminate\Support\Facades\Log;
use Module;
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
    public function creating($modem)
    {
        if (! $modem->qos_id) {
            $modem->qos_id = $modem->contract->qos_id;
        }
    }

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

        if (! $modem->isAltiplano()) {
            $modem->save();  // forces to call the updating() and updated() method of the observer !
        }

        if (! $modem->internet_access) {
            $modem->blockCpeViaDhcp();
        }

        if ($modem->isAltiplano() && Module::collections()->has('Altiplano')) {
            $createOntJob = new \Modules\Altiplano\Jobs\CreateOntIntentJob($modem);
            $createL2Job = new \Modules\Altiplano\Jobs\CreateL2UserIntentJob($modem);
            $createOntJob
                ->withChain([$createL2Job])
                ->catch(function () use ($modem) {
                    Log::error('There was an error creating one or more intents', [$modem]);
                })->dispatch($modem);
        }
    }

    public function updating(Modem $modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        // reminder: on active envia TEL module: moving modem to other contract is not allowed!
        // check if this is running if you decide to implement moving of modems to other contracts
        // watch Ticket LAR-106
        if (Module::collections()->has('ProvVoipEnvia')) {
            // updating is also called on create – so we have to check this
            if ((! $modem->wasRecentlyCreated) && $modem->isDirty('contract_id')) {
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

        // special handling for SmartONT devices
        if (Module::collections()->has('SmartOnt')) {
            if ($modem->netgw_id) {
                if ($modem->configfile->is_multiservice_ont) {
                    $unchangables = [
                        'configfile_id',
                        'mac',
                        'serial_num',
                    ];
                    $modem->restoreUnchangeableFields($unchangables, trans('messages.ontIsActive'));
                } elseif ('smartont' == $modem->qos->type) {
                    if ($modem->service_port_id) {
                        $unchangables = [
                            'configfile_id',
                            'mac',
                            'qos_id',
                            'serial_num',
                        ];
                        $modem->restoreUnchangeableFields($unchangables, trans('messages.ontIsActive'));
                    }
                }
            }
            if ($modem->configfile->is_multiservice_ont || ('smartont' == $modem->qos->type)) {
                if (array_key_exists('contract_id', $diff)) {
                    $modem->salutation = $modem->contract->salutation;
                    $modem->company = $modem->contract->company;
                    $modem->department = $modem->contract->department;
                    $modem->firstname = $modem->contract->firstname;
                    $modem->lastname = $modem->contract->lastname;
                    $modem->country_code = $modem->contract->country_code;
                    $modem->zip = $modem->contract->zip;
                    $modem->city = $modem->contract->city;
                    $modem->district = $modem->contract->district;
                    $modem->street = $modem->contract->street;
                    $modem->house_number = $modem->contract->house_number;

                    // use special format; later used as “description” in provisioning process
                    // modules/SmartOnt/Console/SnmpTrapHandlerCommand.php
                    $name_parts = [
                        'serial' => $modem->serial_num,
                        'zip' => $modem->zip,
                        'street' => $modem->street,
                        'house_number' => $modem->house_number,
                    ];

                    // Attention: length of description field on Huawei OLT is limited to 64
                    // if the resulting name is to long we shorten the street and
                    // not the whole string (in which case we would lose the housenumber)
                    do {
                        $name = implode('  ', $name_parts);
                        $name = \Str::ascii($name);
                        $name = preg_replace('/[^\.a-zA-Z0-9_-]/', '_', $name);
                        $name_parts['street'] = substr($name_parts['street'], 0, -1);
                    } while (strlen($name) > 64);

                    $modem->name = $name;
                }
            }
        }

        if (! ($modem->wasRecentlyCreated && $modem->lng && $modem->lat && $modem->geocode_source)) {
            $modem->setGeocodes();
        }

        // check if more values have changed – especially “x” and “y” which refreshes MPR
        $diff = $modem->getDirty();

        // Refresh MPS rules
        // Note: does not perform a save() which could trigger observer.
        if (Module::collections()->has('HfcCustomer')) {
            if (multi_array_key_exists(['lng', 'lat'], $diff)) {
                \Queue::pushOn('medium', new \Modules\HfcCustomer\Jobs\MpsJob($modem));
            }
        }

        if (multi_array_key_exists(['mac', 'configfile_id'], $diff)) {
            $modem->ipv4 = null;
        }
    }

    public function updated($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        if (! $modem->observer_enabled) {
            return;
        }

        $diff = $modem->getDirty();

        // Only restart, make dhcp and configfile and only restart dhcpd via systemdobserver when it's necessary
        if (multi_array_key_exists(['contract_id', 'public', 'internet_access', 'configfile_id', 'qos_id', 'mac', 'serial_num'], $diff)) {
            Modem::create_ignore_cpe_dhcp_file();
            $modem->make_dhcp_cm();

            if (! $modem->wasRecentlyCreated) {
                $macChanged = array_key_exists('mac', $diff);

                if (multi_array_key_exists(['internet_access', 'mac'], $diff)) {
                    $modem->blockCpeViaDhcp(boolval($modem->internet_access), $macChanged);
                }

                $modem->restart_modem($macChanged);
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

        if ($modem->isAltiplano() && Module::collections()->has('Altiplano')) {
            if ($modem->isDirty('qos_id')) {
                $deleteL2UserJob = new \Modules\Altiplano\Jobs\DeleteL2UserIntentJob($modem);
                $createL2Job = new \Modules\Altiplano\Jobs\CreateL2UserIntentJob($modem);
                $deleteL2UserJob
                    ->withChain([$createL2Job])
                    ->catch(function () use ($modem) {
                        Log::error('There was an error creating one or more intents', [$modem]);
                    })->dispatch($modem);

                return;
            }

            if ((bool) $modem->getOriginal('internet_access') !== (bool) $modem->internet_access) {
                if ($modem->internet_access) {
                    Log::error('internet_access activated. Try and create L2User.');
                    \Queue::pushOn('high', new \Modules\Altiplano\Jobs\CreateL2UserIntentJob($modem));
                } else {
                    Log::error('internet_access deactivated. Try and delete L2User.');
                    \Queue::pushOn('high', new \Modules\Altiplano\Jobs\DeleteL2UserIntentJob($modem));
                }
            }
        }

        // special handling for SmartONT devices
        if (\Module::collections()->has('SmartOnt')) {
            if ($modem->configfile->is_multiservice_ont || ('smartont' == $modem->qos->type)) {
                if (array_key_exists('contract_id', $diff) && (! is_null($modem->netgw_id))) {
                    Log::debug(__METHOD__.' pushing \Modules\SmartOnt\Jobs\RemoveOntFromOltJob('.$modem->id.') to queue');
                    \Queue::pushOn('serial', new \Modules\SmartOnt\Jobs\RemoveOntFromOltJob($modem->id));
                }
            }
        }
    }

    public function deleted($modem)
    {
        Log::debug(__METHOD__.' started for '.$modem->hostname);

        // special handling for SmartONT devices
        if (\Module::collections()->has('SmartOnt')) {
            if ($modem->configfile->is_multiservice_ont || ('smartont' == $modem->qos->type)) {
                if (! is_null($modem->netgw_id)) {
                    Log::debug(__METHOD__.' pushing \Modules\SmartOnt\Jobs\RemoveOntFromOltJob('.$modem->id.') to queue');
                    \Queue::pushOn('serial', new \Modules\SmartOnt\Jobs\RemoveOntFromOltJob($modem->id));
                }

                return;
            }
        }

        if ($modem->isTR069()) {
            $modem->deleteGenieAcsProvision();
            $modem->deleteGenieAcsPreset();
            $modem->deleteGenieAcsTasks();
            $modem->factoryReset();
            $modem->deleteGenieAcsDevice();
        }

        $modem->updateRadius();

        Modem::create_ignore_cpe_dhcp_file();
        if (! $modem->internet_access) {
            $modem->blockCpeViaDhcp(true);
        }
        $modem->make_dhcp_cm(true);
        $modem->restart_modem();
        $modem->delete_configfile();

        if ($modem->isAltiplano() && Module::collections()->has('Altiplano')) {
            Log::debug('Queuing delete ONT & L2User intents');
            $deleteL2UserJob = new \Modules\Altiplano\Jobs\DeleteL2UserIntentJob($modem);
            $deleteOntJob = new \Modules\Altiplano\Jobs\DeleteOntIntentJob($modem);
            $deleteL2UserJob
                ->withChain([$deleteOntJob])
                ->catch(function () use ($modem) {
                    Log::error('There was an error deleting the intent', [$modem]);
                })->dispatch($modem);
        }
    }
}
