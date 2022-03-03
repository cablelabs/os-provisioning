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

namespace App\extensions\websockets;

class WebsocketApi
{
    protected $pusherApi;

    public function getPusherApi()
    {
        return \Broadcast::driver('pusher-php')->getPusher();
    }

    /**
     * Check if subscribers are currently listening to the given channel
     * See BeyondCode\LaravelWebSockets\HttpApi\Controllers\FetchChannelsController
     *
     * @param string
     * @param bool
     * @return bool
     */
    public function channelHasSubscribers($channel, $excludeRequestingUser = false)
    {
        if (! $this->pusherApi) {
            $this->pusherApi = $this->getPusherApi();
        }

        $users = $this->pusherApi->get_users_info($channel);
        if (! $users) {
            \Log::debug("$channel channel - Subscribed users: false");

            return false;
        }

        /* Attention: With this the currently initiating user will not be counted as subscriber even when websocket
            connection is already established so that initiating the loop always works. The resulting problem can be
            that the user can initiate multiple loops when opening multiple tabs (at the same time). This should be
            addressed in javascript by not triggering the loop when the tab is hidden
        */
        if ($excludeRequestingUser && isset($users->users[0]) && $users->users[0]->id == auth()->user()->id) {
            unset($users->users[0]);
        }

        \Log::debug("$channel channel - subscribed users: ".json_encode($users->users));

        return $users->users != [];
    }
}
