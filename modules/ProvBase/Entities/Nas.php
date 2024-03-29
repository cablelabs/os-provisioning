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

namespace Modules\ProvBase\Entities;

use Modules\ProvBase\Observers\NetGwObserver;

class Nas extends \BaseModel
{
    protected $connection = 'pgsql-radius';

    // The associated SQL table for this Model
    public $table = 'nas';

    public $timestamps = false;
    protected $forceDeleting = true;

    // freeradius-mysql does not use softdeletes
    public static function bootSoftDeletes()
    {
    }

    public function netgw()
    {
        return $this->belongsTo(NetGw::class, 'shortname');
    }

    /**
     * Truncate nas table and refresh all entries - corresponds to Netgw
     */
    public static function repopulateDb()
    {
        echo "Build netgw related nas table ...\n";

        Nas::truncate();

        $netgws = NetGw::where('type', 'bras')->get();
        $count = $netgws->count();

        foreach ($netgws as $i => $netgw) {
            NetGwObserver::updateNas($netgw);

            echo $i.'/'.$count."\r";
        }
    }
}
