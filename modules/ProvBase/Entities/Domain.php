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

class Domain extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'domain';

    // Name of View
    public static function view_headline()
    {
        return 'Domains';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-tag"></i>';
    }

    // There are no validation rules
    public function rules()
    {
        return [
            'name' => 'required|regex:/^[0-9A-Za-z\.\-\_]+$/',
            'type' => 'required',
        ];
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
            'index_header' => [$this->table.'.name', $this->table.'.type', $this->table.'.alias'],
            'header' =>  'Domain: '.$this->name.' (Type: '.$this->type.')',
            'bsclass' => $bsclass,
            'order_by' => ['0' => 'asc'], ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
    }
}
