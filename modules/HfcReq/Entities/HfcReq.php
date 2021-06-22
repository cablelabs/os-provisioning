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

namespace Modules\HfcReq\Entities;

class HfcReq extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'hfcreq';

    // Add your validation rules here
    public function rules()
    {
        return [
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'HfcReq Config';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'HfcReq';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-random"></i>';
    }
}
