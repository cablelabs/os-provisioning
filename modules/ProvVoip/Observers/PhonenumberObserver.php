<?php

namespace Modules\ProvVoip\Observers;

use Modules\ProvVoip\Entities\Mta;
use Modules\ProvVoip\Entities\ProvVoip;

/**
 * Phonenumber Observer Class
 * Handles changes on Phonenumbers
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class PhonenumberObserver
{
    /**
     * For envia TEL API we create username and login if not given.
     * Otherwise envia TEL will do this – so we would have to ask for this data…
     *
     * @author Patrick Reichel
     */
    protected function _create_login_data($phonenumber)
    {
        if (\Module::collections()->has('ProvVoipEnvia') && ($phonenumber->mta->type == 'sip')) {
            if (! boolval($phonenumber->password)) {
                $phonenumber->password = \Acme\php\Password::generate_password(15, 'envia');
            }
        }
    }

    public function creating($phonenumber)
    {

        // TODO: ATM we don't force the creation of phonenumbermanagements – if we change our mind we can activate this line again
        // on creating there can not be a phonenumbermanagement – so we can set active state to false in each case
        // $phonenumber->active = 0;

        $this->_check_overlapping($phonenumber);
        $this->_create_login_data($phonenumber);
    }

    public function created($phonenumber)
    {
        $this->renewConfig($phonenumber);
    }

    /**
     * Checks if updating the phonenumber is allowed.
     * Used to prevent problems related with envia TEL.
     *
     * @author Patrick Reichel
     */
    protected function _updating_allowed($phonenumber)
    {

        // no envia TEL => no problems
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            return true;
        }

        // else we have to check if both MTAs belong to the same contract and if both modem's installation addresses are the same
        $new_mta = $phonenumber->mta;
        $old_mta = MTA::findOrFail(intval($phonenumber->getOriginal()['mta_id']));

        // if MTA has not changed: no problems
        if ($new_mta->id == $old_mta->id) {
            return true;
        }

        if (! $phonenumber->phonenumber_reassignment_allowed($old_mta->modem, $new_mta->modem)) {
            $msg = trans('validation.reassign_phonenumber_to_mta_fail', ['id' => $new_mta->id]);
            $phonenumber->addAboveMessage($msg, 'error', 'form');

            return false;
        }

        return true;
    }

    public function updating($phonenumber)
    {
        if (! $this->_updating_allowed($phonenumber)) {
            return false;
        }

        $this->_check_nr_change($phonenumber);
        $this->_create_login_data($phonenumber);
    }

    public function updated($phonenumber)
    {
        // uncommented by nino: redundant and senseless here
        // $this->_create_login_data($phonenumber);

        // check if we have a MTA change
        $this->_check_and_process_mta_change($phonenumber);

        // changes on SIP data (username, password, sipdomain) have to be sent to external providers, too
        $this->_check_and_process_sip_data_change($phonenumber);

        // rebuild the current mta's configfile and restart the modem – has to be done in each case
        $this->renewConfig($phonenumber);
    }

    /**
     * Apply changes on assigning a phonenumber to a new MTA.
     *
     * @author Patrick Reichel
     */
    protected function _check_and_process_mta_change($phonenumber)
    {
        $old_mta_id = intval($phonenumber->getOriginal('mta_id'));
        $new_mta_id = intval($phonenumber->mta_id);

        // if the MTA has not been changed we have nothing to do :-)
        if ($old_mta_id == $new_mta_id) {
            return;
        }

        // get an instance of both MTAs for easier access
        $old_mta = Mta::findOrFail($old_mta_id);
        $new_mta = $phonenumber->mta;

        // rebuild old MTA's config and restart the modem (we have to remove all information about this phonenumber)
        $old_mta->make_configfile();
        $old_mta->restart();

        // for all possible external providers we have to check if there is data to update, too
        $this->_check_and_process_mta_change_for_envia($phonenumber, $old_mta, $new_mta);
    }

    /**
     * Change envia TEL related data on assigning a phonenumber to a new MTA.
     * Here we have to decide if the change is permanent (customer got new modem) or temporary (e.g. for testing reasons).
     *
     * @author Patrick Reichel
     */
    protected function _check_and_process_mta_change_for_envia($phonenumber, $old_mta, $new_mta)
    {

        // check if module is enabled
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            return;
        }

        // we need some helpers for easier access
        $old_modem = $old_mta->modem;
        $old_contract = $old_modem->contract;
        $new_modem = $new_mta->modem;
        $new_contract = $new_modem->contract;

        // if the phonenumber does not exist at envia TEL (no management or no external creation date):
        // nothing to cange in modems
        if (
            (! $phonenumber->contract_external_id)
        ) {
            $msg = trans('provvoipenvia::messages.phonenumberNotCreatedAtEnviaNoModemChange');
            $phonenumber->addAboveMessage($msg, 'info', 'form');

            return;
        }

        // the moment we get here we take for sure that we have a permanent switch (defective old modem)
        // now we have to do a bunch of envia TEL data related work

        // first: get all the orders related to the number or the old modem
        // and overwrite the modem_id with the new modem's id
        $phonenumber_related_orders = $phonenumber->enviaorders(true)->get();
        $contract_related_orders = \Modules\ProvVoipEnvia\Entities\EnviaOrder::withTrashed()->where('modem_id', $old_modem->id)->get();

        // build a collection of all orders that need to be changed
        // this are all orders related to the current phonenumber or related to contract but not related to phonenumber (e.g. orders that created other phonenumbers)
        $related_orders = $phonenumber_related_orders;
        while ($tmp_order = $contract_related_orders->pop()) {
            $related_numbers = $tmp_order->phonenumbers;
            if ($related_numbers->isEmpty() || $related_numbers->contains($phonenumber)) {
                $related_orders->push($tmp_order);
            }
        }
        $related_orders = $related_orders->unique();

        // change the modem id to the value of the new modem
        foreach ($related_orders as $order) {
            $order->modem_id = $new_modem->id;
            $order->save();
        }

        // second: write all envia TEL related data from the old to the new modem
        if (! $new_modem->contract_ext_creation_date) {
            $new_modem->contract_ext_creation_date = $old_modem->contract_ext_creation_date;
        } else {
            $new_modem->contract_ext_creation_date = min($new_modem->contract_ext_creation_date, $old_modem->contract_ext_creation_date);
        }
        if (! $new_modem->contract_ext_termination_date) {
            $new_modem->contract_ext_termination_date = $old_modem->contract_ext_termination_date;
        } else {
            $new_modem->contract_ext_termination_date = max($new_modem->contract_ext_termination_date, $old_modem->contract_ext_termination_date);
        }
        $new_modem->save();

        // third: if there are no more numbers attached to the old modem: remove all envia TEL related data
        if (! $old_modem->has_phonenumbers_attached()) {
            $old_modem->remove_envia_related_data();
        } else {
            $attributes = ['target'=>'_blank'];

            // prepare the link (for view) for old modem (this may be useful as we get the breadcrumb for the new modem on our return to phonenumber.edit)
            $parameters = [
                'modem' => $old_modem->id,
            ];
            $title = 'modem '.$old_modem->id.' ('.$old_modem->mac.')';
            $modem_href = \HTML::linkRoute('Modem.edit', $title, $parameters, $attributes);

            // prepare the links to the phonenumbers still related to old modem (they probably also have to be moved to another MTA)
            $numbers = [];
            foreach ($old_modem->mtas as $tmp_mta) {
                foreach ($tmp_mta->phonenumbers->all() as $tmp_phonenumber) {
                    $tmp_parameters = [
                        'phonenumber' => $tmp_phonenumber->id,
                    ];
                    $tmp_title = $tmp_phonenumber->prefix_number.'/'.$tmp_phonenumber->number;
                    $tmp_href = \HTML::linkRoute('Phonenumber.edit', $tmp_title, $tmp_parameters, $attributes);
                    array_push($numbers, $tmp_href);
                }
            }
            $numbers = '<br>&nbsp;&nbsp;'.implode('<br>&nbsp;&nbsp;', $numbers);

            $msg = trans('provvoipenvia::messages.modemStillNumbersAttached', ['href' => $modem_href, 'numbers' => $numbers]);
            $phonenumber->addAboveMessage($msg, 'warning', 'form');
        }
    }

    /**
     * If SIP data has been changed there are probably changes at your provider needed!
     *
     * @author Patrick Reichel
     */
    protected function _check_and_process_sip_data_change($phonenumber)
    {
        if ($phonenumber->isDirty('username', 'password', 'sipdomain')) {
            $this->_check_and_process_sip_data_change_for_envia($phonenumber);
        }
    }

    /**
     * If SIP data has been changed and module ProvVoipEnvia is enabled:
     * Change this data at envia TEL, too
     *
     * @author Patrick Reichel
     */
    protected function _check_and_process_sip_data_change_for_envia($phonenumber)
    {

        // check if module is enabled
        if (! \Module::collections()->has('ProvVoipEnvia')) {
            return;
        }

        // check what changed the SIP data
        if (
            (strpos(\URL::current(), 'request/contract_get_voice_data') !== false)
            ||
            (strpos(\URL::current(), 'cron/contract_get_voice_data') !== false)
        ) {
            // changed through API method get_voice_data: do nothing
            return;
        } else {
            // if we end up here: the current change has been done manually
            // inform the user that he has to change the data at envia TEL, too
            // TODO: check if this data can be changed automagically at envia TEL!
            $parameters = [
                'job' => 'voip_account_update',
                'origin' => urlencode(\URL::previous()),
                'phonenumber_id' => $phonenumber->id,
            ];

            $title = trans('provvoipenvia::messages.doManuallyNow');
            $envia_href = \HTML::linkRoute('ProvVoipEnvia.request', $title, $parameters);

            $msg = trans('provvoipenvia::messages.sipDateNotChangedAutomaticallyAtEnvia', ['href' => $envia_href]);
            $phonenumber->addAboveMessage($msg, 'warning', 'form');
        }
    }

    /**
     * Check if an HL-Komm phonenumber existed within today and the last CDR cycle to warn the user that
     * creating a phonenumber with the same number can lead to wrong charges/accounting statements as there is
     * only a phonenumber stated in the CDR.csv
     */
    private function _check_overlapping($phonenumber)
    {
        if (! $phonenumber->number || ! \Module::collections()->has('BillingBase')) {
            return;
        }

        $sipdomain = $phonenumber->sipdomain ?: ProvVoip::first()->mta_domain;
        $registrar = 'sip.hlkomm.net';

        if (strpos($sipdomain, $registrar) === false) {
            return;
        }

        // check if number already existed within the last month(s)
        $delay = \Modules\BillingBase\Entities\BillingBase::first()->cdr_offset;
        $cdr_first_day_of_month = date('Y-m-01', strtotime('first day of -'.(1 + $delay).' month'));

        $num = \DB::table('phonenumber')
            ->where('prefix_number', '=', $phonenumber->prefix_number)
            ->where('number', '=', $phonenumber->number)
            ->where(function ($query) use ($registrar) {
                $query
                ->where('sipdomain', 'like', "%$registrar%")
                ->orWhereNull('sipdomain')
                ->orWhere('sipdomain', '=', '');
            })
            ->where(function ($query) use ($cdr_first_day_of_month) {
                $query
                ->whereNull('deleted_at')
                ->orWhere('deleted_at', '>=', $cdr_first_day_of_month);
            })
            ->count();

        if ($num) {
            \Session::put('alert.danger', trans('messages.phonenumber_overlap_hlkomm', ['delay' => 1 + $delay]));
        }
    }

    /**
     * After changing an HL-Komm phonenumber it's not possible to assign the corresponding CDRs to the contract anymore
     * So we should warn the user to just do this with test numbers where the customer is not charged
     */
    private function _check_nr_change($phonenumber)
    {
        if (! \Module::collections()->has('BillingBase')) {
            return;
        }

        if (! multi_array_key_exists(['prefix_number', 'number'], $phonenumber->getDirty())) {
            return;
        }

        $sipdomain = $phonenumber->sipdomain ?: ProvVoip::first()->mta_domain;
        $registrar = 'sip.hlkomm.net';

        if (strpos($sipdomain, $registrar) === false) {
            return;
        }

        \Session::put('alert.danger', trans('messages.phonenumber_nr_change_hlkomm'));
    }

    /**
     * Rebuild the current configfile/provision and restart/factoryReset the device
     *
     * @param $phonenumber \Modules\ProvVoip\Entities\Phonenumber
     * @author Ole Ernst
     */
    private function renewConfig($phonenumber)
    {
        if ($phonenumber->mta->modem->isTR069()) {
            $phonenumber->mta->modem->make_configfile();
            $phonenumber->mta->modem->factoryReset();
        } else {
            $phonenumber->mta->make_configfile();
            $phonenumber->mta->restart();
        }
    }

    public function deleted($phonenumber)
    {
        $this->renewConfig($phonenumber);

        // check if this number has been the last on old modem ⇒ if so remove envia related data from modem
        if (! $phonenumber->mta->modem->has_phonenumbers_attached()) {
            $phonenumber->mta->modem->remove_envia_related_data();
        }
    }
}
