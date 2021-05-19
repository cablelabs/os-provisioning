<?php

namespace Modules\HfcSnmp\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

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
