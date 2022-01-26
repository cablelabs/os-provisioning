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

namespace Modules\Dreamfiber\Entities;

class Dreamfiber extends \BaseModel
{
    protected $fillable = [];

    /**
     * The instance of the SOAP client.
     */
    protected $soap = null;

    /**
     * Auth params for a SOAP request.
     */
    protected $soapParamsAuth = [];

    /**
     * Constructor
     *
     * @author Patrick Reichel
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes = []);
        $this->initSoap();
    }

    /**
     * Initializes array holding data for SOAP request
     *
     * @author Patrick Reichel
     */
    protected function initSoap()
    {
        // instantiate a SOAP client
        $this->soap = new \SoapClient(
            config('dreamfiber.api.wsdlFile'),
            [
                'exceptions' => true,   // throw SoapFault on error
                'trace' => 1,           // include tracing information for debugging
            ]
            );

        // data for authentication
        $this->soapParamsAuth = [
            'authentication' => [
                'username' => config('dreamfiber.api.user'),
                'password' => config('dreamfiber.api.password'),
                'userInformation' => config('dreamfiber.api.userInformation'),
            ],
        ];
    }

    public function dreamfiberApi($soapMethod, $soapParams)
    {
        // fire the soapMethod to retrieve data
        try {
            $result = $this->soap->{$soapMethod}($soapParams);
        } catch (\SoapFault $sf) {
            // e.g. on validation error
            $this->logAndPrint('SoapFault thrown: '.$sf->getMessage(), 'error');

            return null;
        } catch (\Exception $ex) {
            $this->logAndPrint('Exceptiona thrown: '.$ex->getMessage(), 'error');
            throw($ex);
        }

        return $result;
    }

    /**
     * Get informations about subscription(s)
     *
     * @param $filter defines which subscriptions we shall ask for
     */
    public function getDfSubscriptionInformation($filter, $option = null)
    {
        $soapParamsSearch = $this->getSearchParams($filter, $option);
        $soapParams = array_merge(
            $this->soapParamsAuth,
            $soapParamsSearch
        );
        $this->logAndPrint("Getting subscription information for $filter subscription(s) $option", 'info');

        $result = $this->dreamfiberApi('getSubscriptionInformation', $soapParams);
        if (! $result) {
            $this->logAndPrint('Got no result', 'error');

            return;
        }

        $msg = $result->message->messageTitle.': '.$result->message->messageDescription;

        // check if api call has been successfull
        if (! $result->success) {
            $this->logAndPrint($msg, 'error');

            return;
        }

        $this->processDfSubscriptionInformation($result);
    }

    /**
     * Use informations retrieved from API to update database.
     *
     * @author Patrick Reichel
     */
    protected function processDfSubscriptionInformation($data)
    {
        $soapSubs = is_array($data->subscription) ? $data->subscription : [$data->subscription];

        foreach ($soapSubs as $soapSub) {
            $soapEvents = is_array($soapSub->subscriptionLog->subscriptionEvent) ? $soapSub->subscriptionLog->subscriptionEvent : [$soapSub->subscriptionLog->subscriptionEvent];

            $dbSub = DfSubscription::with('dfsubscriptionevents')->firstOrNew(['subscription_id' => $soapSub->subscriptionId]);

            if (! $dbSub->fillModelFromApi($soapSub)) {
                // if something went wrong with this subscription: move on to the next
                continue;
            }
            $dbSub->save();

            // check if SubscriptionEvents exist in database; if not: create
            foreach ($soapEvents as $soapEvent) {
                $soapEventInDb = false;
                foreach ($dbSub->dfsubscriptionevents as $dbEvent) {
                    // it should be save to compare all fields – events should not change
                    if (
                        ($soapEvent->timestamp == $dbEvent->timestamp) &&
                        ($soapEvent->status == $dbEvent->status) &&
                        ($soapEvent->description == $dbEvent->description)
                    ) {
                        $soapEventInDb = true;
                        break;
                    }
                }
                if (! $soapEventInDb) {
                    $dbEvent = new DfSubscriptionEvent();
                    $dbEvent->dfsubscription_id = $dbSub->id;
                    $dbEvent->timestamp = $soapEvent->timestamp;
                    $dbEvent->status = $soapEvent->status;
                    $dbEvent->description = $soapEvent->description;
                    $dbEvent->save();
                }
            }
        }
    }

    protected function getSearchParams($filter, $option)
    {
        if ('all' == $filter) {
            return $this->getSearchParamsAll();
        }

        if ('single' == $filter) {
            return $this->getSearchParamsSingleId($option);
        }

        return [];
    }

    /**
     * Get search param to get all subscriptions.
     *
     * @author Patrick Reichel
     */
    protected function getSearchParamsAll()
    {
        return [
            'subscriptionSearchPattern' => [
                'subscriptionEndPointId' => '*',
            ],
        ];
    }

    /**
     * Get search param to get a single subscriptions.
     *
     * @author Patrick Reichel
     */
    protected function getSearchParamsSingleId($sid)
    {
        if (! preg_match('/^[0-9]*$/', $sid, $matches)) {
            throw new \Exception('Subscription ID has to be integer, “'.$sid.'” given');
        }

        return [
            'subscriptionSearchPattern' => [
                'subscriptionId' => $sid,
            ],
        ];
    }
}
