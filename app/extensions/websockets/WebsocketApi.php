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

use Illuminate\Support\Facades\Broadcast;

class WebsocketApi
{
    protected $pusherApi;

    public function __construct()
    {
        $this->pusherApi = Broadcast::driver('pusher-php')->getPusher();
    }

    /**
     * Check if subscribers are currently listening to the given channel
     * See BeyondCode\LaravelWebSockets\HttpApi\Controllers\FetchChannelsController
     *
     * @param string  The name of the channel with type prefix
     * @return bool channel only exists, as long as users are subscribed to it
     */
    public function channelHasSubscribers(string $channel, bool $initial = false): bool
    {
        if (! array_key_exists($channel, $this->pusherApi->getChannels()->channels)) {
            return false;
        }

        if ($initial) {
            return $this->pusherApi->getChannelInfo($channel)->subscription_count > 1;
        }

        return $this->pusherApi->getChannelInfo($channel)->subscription_count;
    }
}
