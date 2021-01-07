<?php

namespace Modules\ProvVoip\Observers;

/**
 * PhonenumberManagement observer class
 * Handles changes on Phonenumbers
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class PhonenumberManagementObserver
{
    public function created($phonenumbermanagement)
    {
        $phonenumbermanagement->phonenumber->set_active_state();
    }

    public function updated($phonenumbermanagement)
    {
        $phonenumbermanagement->phonenumber->set_active_state();
    }

    public function deleted($phonenumbermanagement)
    {
        $phonenumbermanagement->phonenumber->set_active_state();
    }
}
