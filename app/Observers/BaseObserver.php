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
        $this->changeCacheCount($model, __FUNCTION__);

        if (! $model->observer_enabled) {
            return;
        }

        self::addLogEntry($model, __FUNCTION__);

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

        self::addLogEntry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		⇒ see comment at created
        return true;
    }

    public function deleted($model)
    {
        $this->changeCacheCount($model, __FUNCTION__);

        if (! $model->observer_enabled) {
            return;
        }

        self::addLogEntry($model, __FUNCTION__);

        // TODO: analyze impacts of different return values
        //		⇒ see comment at created
        return true;
    }

    public function restored($model)
    {
        $this->changeCacheCount($model, __FUNCTION__);
    }

    /**
     * Create Log Entry on fired Event
     */
    public static function addLogEntry($model, $action)
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

                $original = $model->getRawOriginal($key);
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
            'user_id'   => $user ? $user->id : 0,
            'username'  => $user ? ($user->first_name || $user->last_name) ? $user->first_name.' '.$user->last_name : $user->login_name : 'cronjob',
            'method'    => $action,
            'model'     => $model_name,
            'model_id'  => $model->id,
            'text'      => $text,
        ];

        GuiLog::log_changes($data);
    }

    /**
     * Adapt database model count cached for better index table performance
     */
    private function changeCacheCount($model, $method)
    {
        $count = $model->cachedIndexTableCount;

        if ($method == 'created' || $method == 'restored') {
            $count += 1;
        } elseif ($method == 'deleted') {
            $count -= 1;
        }

        cache(['indexTables.'.$model->table => $count]);
    }
}
