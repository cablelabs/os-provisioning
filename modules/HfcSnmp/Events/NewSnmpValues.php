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

namespace Modules\HfcSnmp\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NewSnmpValues implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $broadcastQueue = 'snmpValues';

    private $data;
    private $netelement;
    private $channel;

    public function __construct($data, $netelement, $paramId, $index)
    {
        $this->data = $data;
        $this->netelement = $netelement;
        $this->channel = self::getChannelName($netelement, $paramId, $index);
    }

    /**
     * Broadcast this data
     *
     * @return array
     */
    public function broadcastAs()
    {
        return 'newSnmpValues';
    }

    public function broadcastWith()
    {
        return [
            'data' => $this->data,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel($this->channel);
    }

    public static function getChannelName($netelement, $paramId, $index)
    {
        return "snmpvalues.$netelement->id.$paramId.$index";
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
