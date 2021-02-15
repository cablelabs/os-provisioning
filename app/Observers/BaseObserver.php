<?php

namespace App\Observers;

use Auth;
use App\GuiLog;

/**
 * Base Observer Class - Logging of all User Interaction
 *
 * @author Nino Ryschawy
 */
class BaseObserver
{
    public function created($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        $this->add_log_entry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		without return (= return null): all is running, but multiple log entries are created
        //		return false: only one log entry per change, but created of e.g. PhonenumberObserver is never called (checked this using dd())
        //		return true: one log entry, other observers are called
        // that are our observations so far – we definitely should check if there are other side effects!!
        // possible hint: the BaseObserver is registered before the model's observers
        return true;
    }

    public function updated($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        $this->add_log_entry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		⇒ see comment at created
        return true;
    }

    public function deleted($model)
    {
        if (! $model->observer_enabled) {
            return;
        }

        $this->add_log_entry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		⇒ see comment at created
        return true;
    }

    /**
     * Create Log Entry on fired Event
     */
    private function add_log_entry($model, $action)
    {
        $user = Auth::user();

        $model_name = $model->get_model_name();

        $text = '';

        $attributes = $model->getDirty();

        if (array_key_exists('remember_token', $attributes)) {
            unset($attributes['remember_token']);
            unset($attributes['updated_at']);
        }

        if (empty($attributes) && is_null($model->deleted_at)) {
            return;
        }

        // if really updated (and not updated by model->save() in observer->created() like in contract)
        if (($action == 'updated') && (! $model->wasRecentlyCreated)) {

            // skip following attributes - TODO:
            $ignore = [
                'updated_at',
            ];

            // hide the changed data (but log the fact of change)
            $hide = [
                'password',
            ];

            // get changed attributes
            $arr = [];

            foreach ($model->getAttributes() as $key => $value) {
                if (in_array($key, $ignore)) {
                    continue;
                }

                $original = $model->getOriginal($key);
                if ($original != $value) {
                    if (in_array($key, $hide)) {
                        $arr[] = $key;
                    } elseif (array_key_exists('deleted_at', $attributes) && $attributes['deleted_at'] == null) {
                        $arr[] = $key.': '.$original.'-> restored';
                        $action = 'restored';
                    } else {
                        $arr[] = $key.': '.$original.'->'.$value;
                    }
                }
            }
            $text = implode(', ', $arr);
        }

        $data = [
            'user_id' 	=> $user ? $user->id : 0,
            'username' 	=> $user ? $user->first_name.' '.$user->last_name : 'cronjob',
            'method' 	=> $action,
            'model' 	=> $model_name,
            'model_id'  => $model->id,
            'text' 		=> $text,
        ];

        GuiLog::log_changes($data);
    }
}
