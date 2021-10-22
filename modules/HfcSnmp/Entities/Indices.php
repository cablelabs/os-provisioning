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

namespace Modules\HfcSnmp\Entities;

class Indices extends \BaseModel
{
    public $table = 'indices';

    // public $guarded = ['name', 'table'];

    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\HfcSnmp\Observers\IndicesObserver);
    }

    // Add your validation rules here
    public function rules()
    {
        if (! \Request::filled('netelement_id')) {
            return [];
        }

        return [
            // netelement_id & parameter_id combination must be unique
            'parameter_id' => 'unique:indices,parameter_id,'.($this->id ?: 0).',id,deleted_at,NULL,netelement_id,'.\Request::input('netelement_id'),
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'Indices';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-header"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $header = isset($this->parameter) ? $this->parameter->id.': '.$this->parameter->oid->name : '';

        return ['table' => $this->table,
            'index_header' => ['parameter.oid.name'],
            'header' => $header,
            'eager_loading' => ['parameter'], ];
    }

    public function view_belongs_to()
    {
        return $this->parameter;
    }

    /**
     * Relations
     */
    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }

    public function netelement()
    {
        return $this->belongsTo(\Modules\HfcReq\Entities\NetElement::class, 'netelement_id');
    }
}
