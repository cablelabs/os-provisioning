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

use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;
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

    public function updating($qos)
    {
        // SmartOnt: To not confuse the provsioning logic
        // changes of certain fields are not allowed
        if (Module::collections()->has('SmartOnt')) {
            if (! $qos->isInUse()) {
                return;
            }
            $unchangables = [
                'type',
                'vlan_id',
                'ont_line_profile_id',
                'service_profile_id',
                'gem_port',
                'traffic_table_in',
                'traffic_table_out',
            ];
            $qos->restoreUnchangeableFields($unchangables, 'QoS is in use');
            return;
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
