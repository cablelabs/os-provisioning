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

use Module;
use Modules\ProvBase\Entities\Contract;
use Session;

/**
 * Observer Class
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ContractObserver
{
    public function creating(Contract $contract)
    {
        if (! Module::collections()->has('BillingBase')) {
            $contract->sepa_iban = strtoupper($contract->sepa_iban);
            $contract->sepa_bic = strtoupper($contract->sepa_bic);
        }

        // set geocode
        if (! ($contract->lng && $contract->lat)) {
            $contract->setGeocodes();
        }
    }

    public function created($contract)
    {
        $contract->pushToModems(); 	// should not run, because a new added contract can not have modems..

        $contract->updateAddressFromProperty();
    }

    public function updating($contract)
    {
        $original_number = $contract->getRawOriginal('number');
        $original_costcenter_id = $contract->getRawOriginal('costcenter_id');

        // set geocode
        if (! ($contract->wasRecentlyCreated && $contract->lng && $contract->lat && $contract->geocode_source)) {
            $contract->setGeocodes();
        }

        if (! Module::collections()->has('BillingBase')) {
            $contract->sepa_iban = strtoupper($contract->sepa_iban);
            $contract->sepa_bic = strtoupper($contract->sepa_bic);
        }
    }

    public function updated(Contract $contract)
    {
        if (! $contract->observer_enabled) {
            return;
        }

        $changed_fields = $contract->getDirty();

        if (array_key_exists('number', $changed_fields)) {
            // change customer information - take care - this automatically changes login psw of customer
            if (Module::collections()->has('BillingBase') && Module::collections()->has('Ccc') && $customer = $contract->cccUser) {
                $customer->update();
            }
        }

        // Set all related items start date to contracts start date if this behaviour is wished via global config
        if (array_key_exists('contract_start', $changed_fields) && Module::collections()->has('BillingBase')) {
            $conf = cache('billingBase');

            if ($conf->adapt_item_start) {
                // Note: Calling item->save() is not necessary as contract->daily_conversion is called after and manages everything that is to do
                \Modules\BillingBase\Entities\Item::where('contract_id', $contract->id)->update(['valid_from' => $contract->contract_start]);
            }
        }

        if (array_key_exists('contract_start', $changed_fields) || array_key_exists('contract_end', $changed_fields)) {
            $contract->daily_conversion();

            if (Module::collections()->has('BillingBase') && $contract->contract_end && array_key_exists('contract_end', $changed_fields)) {
                // Alert if end is lower than tariffs end of term
                $ret = $contract->getCancelationDates();

                if ($ret['end_of_term'] && $contract->contract_end < $ret['end_of_term']) {
                    Session::put('alert.danger', trans('messages.contract.early_cancel', ['date' => $ret['end_of_term']]));
                }

                // Show alert when contract is canceled and there are yearly payed items that were charged
                // already (by probably full amount) - customer should get a credit then
                $query = $contract->items()->join('product as p', 'item.product_id', '=', 'p.id')
                        ->where('p.billing_cycle', 'Yearly');

                if (date('Y', strtotime($contract->contract_end)) == date('Y')) {
                    $query = $query->where('payed_month', '!=', 0);
                } elseif (date('m') == '01' && $contract->contract_end != date('Y-12-31', strtotime('last year')) &&
                    date('Y', strtotime($contract->contract_end)) == (date('Y') - 1)
                ) {
                    // e.g. in january of current year the user enters belatedly a cancelation date of last year in dec
                } else {
                    return;
                }

                $concede_credit = $query->count();

                if ($concede_credit) {
                    Session::put('alert.warning', trans('messages.contract.concede_credit'));
                }
            }
        }

        if (! Module::collections()->has('BillingBase') &&
            (array_key_exists('internet_access', $changed_fields) || array_key_exists('qos_id', $changed_fields) || array_key_exists('has_telephony', $changed_fields))) {
            $contract->pushToModems(array_key_exists('has_telephony', $changed_fields));
        }

        if (multi_array_key_exists(['realty_id', 'apartment_id'], $changed_fields)) {
            $contract->updateAddressFromProperty();
        }

        if (Module::collections()->has('BillingBase') && Module::collections()->has('Ccc') && $contract->cccUser) {
            $newsletter = \Request::get('newsletter') ?? false;

            if (boolval($contract->cccUser->newsletter) != $newsletter) {
                $contract->cccUser->newsletter = $newsletter;
                $contract->cccUser->save();
            }
        }
    }

    public function deleting($contract)
    {
        if (Module::collections()->has('BillingBase') && Module::collections()->has('Ccc') && $contract->cccUser) {
            $contract->cccUser->delete();
        }
    }
}
