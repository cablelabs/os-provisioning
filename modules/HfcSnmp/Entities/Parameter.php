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

class Parameter extends \BaseModel
{
    public $table = 'parameter';

    public $guarded = ['name', 'table'];
    protected $with = ['oid'];

    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\HfcSnmp\Observers\ParameterObserver);
    }

    // Add your validation rules here
    public function rules()
    {
        return [
            'html_frame' => ['nullable', 'numeric', 'min:1'],
            'html_id' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'Parameter';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-dot-circle-o"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        return ['table' => $this->table,
            'index_header' => ['oid.name', 'oid.oid',  'oid.access'],
            'header' =>  $this->label(),
            'bsclass' => $this->get_bsclass(),
            'eager_loading' => ['oid'], ];
    }

    public function get_bsclass()
    {
        $bsclass = 'warning';

        if (isset($this->oid) && $this->oid->access == 'read-only') {
            $bsclass = 'info';
        }

        return $bsclass;
    }

    public function label()
    {
        return ($this->oid ? $this->oid->oid : '').
            ($this->oid ? ' - '.$this->oid->name : '').
            ($this->oid && $this->oid->name_gui ? ' - '.$this->oid->name_gui : '');
    }

    public function view_has_many()
    {
        $ret = [];

        if ($this->oid->oid_table) {
            $ret['Edit']['SubOIDs']['view']['view'] = 'hfcreq::NetElementType.parameters';
            $ret['Edit']['SubOIDs']['view']['vars']['list'] = $this->children()->orderBy('third_dimension')->orderBy('html_id')->orderBy('id')->get() ?: [];
        }

        return $ret;
    }

    public function view_belongs_to()
    {
        return $this->netelementtype;
    }

    /**
     * Relations
     */
    public function oid()
    {
        return $this->belongsTo(OID::class, 'oid_id');
    }

    public function netelementtype()
    {
        return $this->belongsTo(\Modules\HfcReq\Entities\NetElementType::class, 'netelementtype_id');
    }

    public function indices()
    {
        return $this->hasOne(Indices::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
