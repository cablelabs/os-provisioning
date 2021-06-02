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

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Domain;

class DomainController extends \BaseController
{
    public function view_form_fields($model = null)
    {
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'URL'],
            ['form_type' => 'text', 'name' => 'alias', 'description' => 'Aliases', 'help' => 'aliases seperated by semicolon'],
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => Domain::getPossibleEnumValues('type')],
        ];
    }
}
