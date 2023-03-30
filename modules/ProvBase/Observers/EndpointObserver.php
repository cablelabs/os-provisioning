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

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\RadIpPool;
use Modules\ProvBase\Entities\RadReply;

class EndpointObserver
{
    public function creating($endpoint)
    {
        if (! $endpoint->fixed_ip) {
            $endpoint->ip = null;
        }
    }

    public function created($endpoint)
    {
        self::reserveAddress($endpoint);
        self::releaseIp($endpoint);

        $endpoint->makeDhcp();
        $endpoint->makeNetGwConf();
        $endpoint->nsupdate();

        $endpoint->modem->restart_modem();
    }

    public function updating($endpoint)
    {
        if (! $endpoint->fixed_ip) {
            $endpoint->ip = null;
        }
        $endpoint->nsupdate(true);
    }

    public function updated($endpoint)
    {
        self::reserveAddress($endpoint);

        $endpoint->makeDhcp();
        $endpoint->makeNetGwConf();
        $endpoint->nsupdate();

        $endpoint->modem->restart_modem();
    }

    public function deleted($endpoint)
    {
        self::reserveAddress($endpoint);

        $endpoint->makeDhcp();
        $endpoint->makeNetGwConf();
        $endpoint->nsupdate(true);

        $endpoint->modem->restart_modem();
    }

    /**
     * Handle changes of reserved ip addresses based on endpoints
     * This is called on created/updated/deleted in Endpoint observer
     *
     * @author Ole Ernst
     */
    private static function reserveAddress($endpoint)
    {
        // delete radreply containing Framed-IP-Address
        $endpoint->modem->radreply()->delete();

        // add / update unreserved ip address
        if ($endpoint->getRawOriginal('ip')) {
            RadIpPool::updateOrCreate(
                ['framedipaddress' => $endpoint->getRawOriginal('ip')],
                ['pool_name' => 'CPEPub', 'username' => '']
            );
        }

        if ($endpoint->deleted_at || ! $endpoint->ip || ! $endpoint->modem->isPPP()) {
            return;
        }

        // add new radreply
        $reply = new RadReply;
        $reply->username = $endpoint->modem->ppp_username;
        $reply->attribute = 'Framed-IP-Address';
        $reply->op = ':=';
        $reply->value = $endpoint->ip;
        $reply->save();

        // remove reserved ip address from ippool
        RadIpPool::where('framedipaddress', $endpoint->ip)->delete();
    }

    /**
     * Release IP of the Modem if it is assigned to an endpoint
     *
     * @author Roy Schneider
     *
     * @param  Modules\ProvBase\Entities\Endpoint  $endpoint
     */
    private static function releaseIp($endpoint)
    {
        $lease = $endpoint->modem::searchLease($endpoint->ip.' ');
        $validation['text'] = $lease;
        $validation = Modem::validateLease($validation);

        if (! $lease || $validation['state'] == 'red') {
            return;
        }

        preg_match('/cm_mac = "(.+?)";/', $lease[0], $mac);
        Modem::where('mac', $mac[1])->first()?->restart_modem();
    }
}
