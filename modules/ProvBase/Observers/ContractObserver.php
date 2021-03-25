<?php

namespace Modules\ProvBase\Observers;

use Module;
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
    public function creating($contract)
    {
        if (! Module::collections()->has('BillingBase')) {
            $contract->sepa_iban = strtoupper($contract->sepa_iban);
            $contract->sepa_bic = strtoupper($contract->sepa_bic);
        }
    }

    public function created($contract)
    {
        $contract->pushToModems(); 	// should not run, because a new added contract can not have modems..

        $contract->updateAddressFromProperty();
    }

    public function updating($contract)
    {
        $original_number = $contract->getOriginal('number');
        $original_costcenter_id = $contract->getOriginal('costcenter_id');

        if (! Module::collections()->has('BillingBase')) {
            $contract->sepa_iban = strtoupper($contract->sepa_iban);
            $contract->sepa_bic = strtoupper($contract->sepa_bic);
        }
    }

    public function updated($contract)
    {
        if (! $contract->observer_enabled) {
            return;
        }

        $changed_fields = $contract->getDirty();

        if (isset($changed_fields['number'])) {
            // change customer information - take care - this automatically changes login psw of customer
            if ($customer = $contract->CccUser) {
                $customer->update();
            }
        }

        // Set all related items start date to contracts start date if this behaviour is wished via global config
        if (isset($changed_fields['contract_start']) && Module::collections()->has('BillingBase')) {
            $conf = \Modules\BillingBase\Entities\BillingBase::first();

            if ($conf->adapt_item_start) {
                // Note: Calling item->save() is not necessary as contract->daily_conversion is called after and manages everything that is to do
                \Modules\BillingBase\Entities\Item::where('contract_id', $contract->id)->update(['valid_from' => $contract->contract_start]);
            }
        }

        if (isset($changed_fields['contract_start']) || isset($changed_fields['contract_end'])) {
            $contract->daily_conversion();

            if (Module::collections()->has('BillingBase') && $contract->contract_end && isset($changed_fields['contract_end'])) {
                // Alert if end is lower than tariffs end of term
                $ret = $contract->getCancelationDates();

                if ($ret['end_of_term'] && $contract->contract_end < $ret['end_of_term']) {
                    Session::put('alert.danger', trans('messages.contract.early_cancel', ['date' => $ret['end_of_term']]));
                }
            }
        }

        // Show alert when contract is canceled and there are yearly payed items that were charged already (by probably full amount) - customer should get a credit then
        if (isset($changed_fields['contract_end'])) {
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

        if (multi_array_key_exists(['realty_id', 'apartment_id'], $changed_fields)) {
            $contract->updateAddressFromProperty();
        }
    }

    public function deleting($contract)
    {
        if ($contract->cccUser) {
            $contract->cccUser->delete();
        }
    }
}
