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

use Illuminate\Validation\Rule;

class ModemOption extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'modem_option';

    // Name of View
    public static function view_headline()
    {
        return 'Modem Option';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-hdd-o"></i>';
    }

    // There are no validation rules
    public function rules()
    {
        return [
            'modem_id' => [
                'required',
                Rule::exists('modem', 'id'),
            ],
            'key' => [
                'required',
                // require key to be unique per modem
                Rule::unique($this->table)->ignore($this)->where(function ($query) {
                    return $query
                        ->whereNull('deleted_at')
                        ->where('modem_id', $this->modem->id ?? \Request::get('modem_id'));
                }),
            ],
        ];
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
            'index_header' => [$this->table.'.key', $this->table.'.value', 'modem.id'],
            'header' =>  "{$this->key}:{$this->value}",
            'bsclass' => $bsclass,
            'eager_loading' => ['modem'],
        ];
    }

    public function get_bsclass()
    {
        return 'success';
    }

    public function modem()
    {
        return $this->belongsTo(Modem::class);
    }

    public function view_belongs_to()
    {
        return $this->modem;
    }
}
