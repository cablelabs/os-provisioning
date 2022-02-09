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

namespace Modules\Dreamfiber\Http\Controllers;

use Modules\Dreamfiber\Entities\Dreamfiber;
use Modules\Dreamfiber\Entities\DfSubscription;

class DreamfiberController extends \BaseController
{
    /**
     * Defines the formular fields for the edit and create view
     *
     * @author Patrick Reichel
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'default_service_name', 'description' => 'default_service_name'],
            ['form_type' => 'text', 'name' => 'default_service_type', 'description' => 'default_service_type'],
        ];
    }

    /**
     * Performs an API action.
     *
     * @author Patrick Reichel
     */
    public function apiAction()
    {
        $action = \Request::get('type');
        $subscription_id = \Request::get('subscription_id');
        $this->subscription = DfSubscription::find($subscription_id);

        // invalid subsription id given
        if (is_null($this->subscription)) {
            \Session::push('tmp_error_above_index_list', 'ERROR: Could not find requested DfSubscription with ID '.$subscription_id);

            return \Redirect::route('DfSubscription.index');
        }

        // invalid api method requested
        if (! in_array($action, $this->subscription->possibleApiActions())) {
            $this->subscription->addAboveMessage('ERROR: Action '.$action.' not allowed for this DfSubcription', 'error');

            return \Redirect::route('DfSubscription.edit', $subscription_id);
        }

        $this->model = new Dreamfiber();

        return $this->{$action}();
    }

    /**
     * Gets informations about subscription(s) from Dreamfiber API
     *
     * @author Patrick Reichel
     */
    public function getSubscriptionInformation()
    {
        $this->model->getDfSubscriptionInformation('single', $this->subscription->subscription_id);

        /* if ($ret) { */
        /*     $this->subscription->addAboveMessage('Subscription information returned by Dreamfiber API', 'success'); */
        /* } else { */
        /*     $this->subscription->addAboveMessage('Dreamfiber API call', 'error'); */
        /* } */

        /* d('boo'); */

        /* d(\Redirect::route('DfSubscription.edit', [$this->subscription->id])); */
        /* d(\Redirect::back()); */
        return \Redirect::route('DfSubscription.edit', [$this->subscription->id]);
    }

    /**
     * Creates a subscription at Dreamfiber API.
     *
     * @author Patrick Reichel
     */
    public function createSubscription()
    {
    }

    /**
     * Updates a subscription at Dreamfiber API.
     *
     * @author Patrick Reichel
     */
    public function updateSubscription()
    {
    }

    /**
     * Canceles a subscription at Dreamfiber API.
     *
     * @author Patrick Reichel
     */
    public function cancelSubscription()
    {
    }

    /**
     * Terminates a subscription at Dreamfiber API.
     *
     * @author Patrick Reichel
     */
    public function terminateSubscription()
    {
    }
}
