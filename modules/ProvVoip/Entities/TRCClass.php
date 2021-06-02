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

namespace Modules\ProvVoip\Entities;

class TRCClass extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'trcclass';

    // Don't forget to fill this array
    protected $fillable = [
        'trc_id',
        'trc_short',
        'trc_description',
    ];

    public function phonenumbermanagements()
    {
        return $this->hasMany(PhonenumberManagement::class);
    }

    public static function trcclass_list_for_form_select()
    {
        $result = [];

        foreach (self::orderBy('trc_id')->get() as $trc) {
            $id = $trc->id;
            $short = $trc->trc_short;
            $desc = $trc->trc_description;

            $result[$id] = $short.': '.$desc;
        }

        return $result;
    }
}
