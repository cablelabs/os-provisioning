<?php

namespace Modules\ProvBase\Observers;

use Modules\ProvBase\Entities\RadGroupReply;

/**
 * Qos Observer Class
 * Handles changes on QoSs
 */
class QosObserver
{
    public function __construct()
    {
        $file = storage_path('app/config/provbase/radius/attributes.php');
        $this->radiusAttributes = file_exists($file) ? require $file : RadGroupReply::$radiusAttributes;
    }

    public function created($qos)
    {
        foreach ($this->radiusAttributes as $key => $attributes) {
            foreach ($attributes as $attribute) {
                if (! $qos->{$key}) {
                    continue;
                }
                self::addRadGroupReply($qos, $attribute, $key);
            }
        }
    }

    public function updated($qos)
    {
        // update only ds/us if their values were changed
        foreach (array_intersect_key($this->radiusAttributes, $qos->getDirty()) as $key => $attributes) {
            foreach ($attributes as $attribute) {
                $reply = $qos->radgroupreplies()->where('attribute', $attribute[0])->where('value', 'like', $attribute[3]);

                // value might be null, since not all QoS values are required (e.g. DS/US QoS name)
                if ($qos->{$key}) {
                    if ($reply->count()) {
                        $reply->update(['value' => sprintf($attribute[2], $qos->{$key})]);
                    } else {
                        self::addRadGroupReply($qos, $attribute, $key);
                    }
                } else {
                    $reply->delete();
                }
            }
        }
    }

    public function deleted($qos)
    {
        $qos->radgroupreplies()->delete();
    }

    /**
     * Add a RadGroupReply
     *
     * @author: Ole Ernst
     */
    private static function addRadGroupReply($qos, $attribute, $key)
    {
        $new = new RadGroupReply;
        $new->groupname = $qos->id;
        $new->attribute = $attribute[0];
        $new->op = $attribute[1];
        $new->value = sprintf($attribute[2], $qos->{$key});
        $new->save();
    }
}
