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

class NetElementType extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'netelementtype';

    /**
     * These Types are relevant for whole Entity Relation Diagram and therefore must not be deleted
     * Furthermore they are ordered by there Database ID which is probably used as fix value in many places of the source code
     * So don't change this order unless you definitly know what you are doing !!!
     */
    public static $undeletables = [
        1 => 'Net',
        2 => 'Cluster',
        3 => 'NetGw',
        4 => 'Amplifier',
        5 => 'Node',
        6 => 'Data',
        7 => 'UPS',
        8 => 'Tap',
        9 => 'Tap-Port',
        10 => 'NMSPrime HA slave',
        11 => 'Passives',
        12 => 'Splitter',
        13 => 'Amplifier',
        14 => 'Node',
        15 => 'RKM-Server',
        16 => 'Market',
        17 => 'Hub',
        18 => 'CCAP Core',
        19 => 'Core Leaf',
        20 => 'Spine',
        21 => 'Node Leaf',
        22 => 'RPD',
        23 => 'CPE',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\HfcReq\Observers\NetElementTypeObserver);
    }

    // Add your validation rules here
    public function rules()
    {
        $id = $this->id ?: 0;

        return [
            'name' => ['required'],
            'sidebar_pos' => ["unique:netelementtype,sidebar_pos,{$id},id,deleted_at,NULL", 'nullable', 'numeric'],
        ];
    }

    /**
     * View Stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'NetElementType';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-object-group"></i>';
    }

    // icon type for tree view
    public function get_icon_type()
    {
        return $this->base_type_id;
    }

    // Deprecated
    public function view_index_label()
    {
        return $this->label();
    }

    public function label()
    {
        return $this->name.($this->version ? "({$this->version})" : '');
    }

    // returns all objects that are related to a DeviceType
    public function view_has_many()
    {
        $threshhold = config('datatables.relationThreshhold');
        $this->setRelation('netelements', $this->netelements()->limit($threshhold)->get());
        $this->netelements_count = $this->netelements->count();

        $ret['Edit']['NetElement']['class'] = 'NetElement';
        $ret['Edit']['NetElement']['count'] = $this->netelements_count;
        $ret['Edit']['NetElement']['relation'] = $this->netelements_count >= $threshhold ?
            collect([new \Modules\HfcReq\Entities\NetElement()]) :
            $this->netelements;

        if (\Module::collections()->has('HfcSnmp') && ! in_array($this->name, self::$undeletables)) {
            // Extra view for easier attachment (e.g. attach all oids from one mibfile)
            $ret['Edit']['Parameters']['view']['view'] = 'hfcreq::NetElementType.parameters';
            $ret['Edit']['Parameters']['view']['vars']['class'] = 'Parameter';
            $ret['Edit']['Parameters']['view']['vars']['count'] = $this->parameters->count();
            $ret['Edit']['Parameters']['view']['vars']['list'] = $this->parameters ?: [];

            // Extra view for easier controlling view structure setting (html_frame, html_id of parameter)
            $ret['Parameter Settings']['Settings']['view']['view'] = 'hfcreq::NetElementType.settings';
            $ret['Parameter Settings']['Settings']['view']['vars']['list'] = $this->parameterList();
        }

        return $ret;
    }

    /**
     * Relations
     */
    public function netelements()
    {
        return $this->hasMany(NetElement::class, 'netelementtype_id');
    }

    public function parameters()
    {
        return $this->hasMany(\Modules\HfcSnmp\Entities\Parameter::class, 'netelementtype_id');
        // return $this->hasMany(\Modules\HfcSnmp\Entities\Parameter::class, 'netelementtype_id')->orderBy('oid_id')->orderBy('id');
    }

    // only for preconfiguration of special device types (e.g. kathreins vgp)
    public function oid()
    {
        return $this->belongsTo(\Modules\HfcSnmp\Entities\OID::class, 'pre_conf_oid_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function baseType()
    {
        return $this->belongsTo(self::class, 'base_type_id');
    }

    /**
     * Format Parent (NetElementTypes) for Select 2 field and allow searching.
     *
     * @param  string|null  $search
     * @request param model The id of the model or null if in create context
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Parent(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        $modelId = request('model') ?? 1;

        return self::select('id', 'name as text')
            ->whereNotIn('id', [1, 2, 8, 9, $modelId])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            });
    }

    /**
     * Format OIDs for Select 2 field and allow for searching.
     *
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Oids(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        return \Modules\HfcSnmp\Entities\OID::select('id', 'name_gui as count')
            ->selectRaw('CONCAT(oid, \' - \', name) as text')
            ->when($search, function ($query, $search) {
                return $query->where('oid', 'like', "%{$search}%")
                    ->where('name', 'like', "%{$search}%")
                    ->where('name_gui', 'like', "%{$search}%");
            });
    }

    public function parameterList()
    {
        $list = [];
        $params = $this->parameters;

        if (! $params) {
            return $list;
        }

        foreach ($params as $param) {
            $list[$param->id] = $param->oid->gui_name ? $param->oid->oid.' - '.$param->oid->gui_name : $param->oid->oid.' - '.$param->oid->name;
        }

        return $list;
    }

    /**
     * Must be defined to disable delete Checkbox on index tree view.
     * Only deletable if there is no netelement assigned.
     *
     * @author Roy Schneider
     *
     * @return array
     */
    public static function undeletables()
    {
        return self::has('netelements')
            ->orWhereHas('children', function ($query) {
                $query->whereHas('netelements');
            })
            ->pluck('name', 'id')
            ->union(self::$undeletables)
            ->keys()
            ->toArray();
    }

    public static function scopeRootNodes()
    {
        return self::whereIn('id', array_keys(self::$undeletables))
            ->orWhereDoesntHave('parent');
    }
}
