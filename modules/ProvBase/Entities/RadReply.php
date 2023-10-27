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

class RadReply extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'radreply';
    protected $connection = 'pgsql-radius';

    public $timestamps = false;
    protected $forceDeleting = true;

    // freeradius-mysql does not use softdeletes
    public static function bootSoftDeletes()
    {
    }

    /**
     * Truncate radreply table and refresh all entries
     */
    public static function repopulateDb()
    {
        RadReply::truncate();

        $observer = new Modules\ProvBase\Observers\EndpointObserver;

        $pppEndpoints = Endpoint::whereHas('modem', function ($q) {
            $q->whereNotNull('ppp_username');
        })->get();

        foreach ($pppEndpoints as $pppEndpoint) {
            $observer->reserveAddress($pppEndpoint);
        }
    }
}
