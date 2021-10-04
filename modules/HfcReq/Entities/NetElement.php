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

use Auth;
use Kalnoy\Nestedset\NodeTrait;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\SnmpAccessException;

class NetElement extends \BaseModel
{
    use NodeTrait;

    // Do not delete children (modem, mta, phonenmumber, etc.)!
    protected $delete_children = false;

    public const SNMP_VALUES_STORAGE_REL_DIR = 'data/hfc/snmpvalues/';

    // The associated SQL table for this Model
    public $table = 'netelement';
    // Always get netelementtype with it to reduce DB queries as it's very probable that netelementtype is queried
    protected $with = ['netelementtype'];

    public $guarded = ['kml_file_upload'];

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql';

    public $kml_path = 'app/data/hfcbase/kml_static';
    private $max_parents = 50;

    public $snmpvalues = ['attributes' => [], 'original' => []];

    // Add your validation rules here
    public function rules()
    {
        $rules = [
            'name' => 'required|string',
            'pos' => 'nullable|geopos',
            'community_ro' => 'nullable|regex:/(^[A-Za-z0-9_]+$)+/',
            'community_rw' => 'nullable|regex:/(^[A-Za-z0-9_]+$)+/',
            'netelementtype_id' => 'required|exists:netelementtype,id,deleted_at,NULL|min:1',
            'agc_offset' => 'nullable|numeric|between:-99.9,99.9',
        ];

        return $rules;
    }

    public static function boot()
    {
        parent::boot();

        self::observe(new \Modules\HfcReq\Observers\NetElementObserver);
    }

    // Name of View
    public static function view_headline()
    {
        return 'NetElement';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-object-ungroup"></i>';
    }

    // Relations
    public function view_has_many()
    {
        $ret = [];

        // if (Module::collections()->has('ProvBase'))
        // {
        // 	$ret['Edit']['Modem']['class'] 	  = 'Modem';
        // 	$ret['Edit']['Modem']['relation'] = $this->modems;
        // }

        if (Module::collections()->has('HfcCustomer')) {
            if ($this->netelementtype->base_type != 9) {
                $ret['Edit']['Mpr']['class'] = 'Mpr';
                $ret['Edit']['Mpr']['relation'] = $this->mprs;
            }
        }

        if (Module::collections()->has('HfcSnmp')) {
            if ($this->netelementtype && ($this->netelementtype->id == 2 || $this->netelementtype->parameters()->count())) {
                $ret['Edit']['Indices']['class'] = 'Indices';
                $ret['Edit']['Indices']['relation'] = $this->indices;
            }

            // see NetElementController@controlling_edit for Controlling Tab!
        }

        if ($this->netelementtype->base_type == 8) {
            $ret['Edit']['SubNetElement']['class'] = 'NetElement';
            $ret['Edit']['SubNetElement']['relation'] = $this->children;
        }

        $this->addViewHasManyTickets($ret);

        return $ret;
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $ret = ['table' => $this->table,
            'index_header' => [$this->table.'.id', 'netelementtype.name', $this->table.'.name', $this->table.'.ip', $this->table.'.pos', $this->table.'.options'],
            'header' => $this->label(),
            'bsclass' => $this->get_bsclass(),
            'eager_loading' => ['netelementtype:id,name'],
            'edit' => ['netelementtype.name' => 'get_elementtype_name'],
        ];

        if (Module::collections()->has('HfcBase') &&
            \Modules\HfcBase\Entities\IcingaObject::db_exists()) {
            array_push($ret['eager_loading'],
                'icingaObject:object_id,name1,is_active',
                'icingaObject.hostStatus:host_object_id,hoststatus_id,last_hard_state',
            );
        }

        return $ret;
    }

    public function get_bsclass()
    {
        if (in_array($this->netelementtype_id, [1, 2])) {
            return 'info';
        }

        if ($this->netelementtype && $this->netelementtype_id == 9) {
            switch ($this->state) {
                case 'C': // off

                    return 'danger';
                case 'B': // attenuated

                    return 'warning';
                default: // on

                    return 'success';
            }
        }

        if (! Module::collections()->has('HfcBase') ||
            (! array_key_exists('icingaObject', $this->relations) &&
            ! \Modules\HfcBase\Entities\IcingaObject::db_exists())) {
            return 'warning';
        }

        $icingaObj = $this->icingaObject;
        if ($icingaObj && $icingaObj->is_active) {
            $icingaObj = $icingaObj->hostStatus;
            if ($icingaObj) {
                return $icingaObj->last_hard_state ? 'danger' : 'success';
            }
        }

        return 'warning';
    }

    public function getBsClassAttribute(): string
    {
        return $this->get_bsclass();
    }

    public function label()
    {
        if (! $this->netelementtype) {
            return "{$this->id} - {$this->name}";
        }

        return "{$this->netelementtype->name}: {$this->name}";
    }

    //for empty relationships
    public function get_elementtype_name()
    {
        return $this->netelementtype ? $this->netelementtype->name : '';
    }

    public function view_belongs_to()
    {
        $ret = new \Illuminate\Database\Eloquent\Collection([$this->netelementtype]);

        if (Module::collections()->has('PropertyManagement') && $this->apartment) {
            $ret->add($this->apartment);
        }

        if ($this->parent) {
            $ret->add($this->parent);
        }

        return $ret;
    }

    /**
     * Scopes
     */

    /**
     * Scope to receive active connected Modems with several important counts.
     *
     * @param  Illuminate\Database\Query\Builder  $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeWithActiveModems($query, $field = 'id', $operator = '>', $id = 2, $minify = false)
    {
        if ($minify) {
            $query->select(['id', 'id_name', 'name', 'ip', 'cluster', 'net', 'netelementtype_id', 'netgw_id', 'parent_id', 'link', 'descr', 'pos']);
        }

        return $query->where($field, $operator, $id)
            ->orderBy('pos')
            ->withCount([
                'modems' => function ($query) {
                    $this->excludeCanceledContractsQuery($query);
                },
                'modems as modems_online_count' => function ($query) {
                    $query->where('us_pwr', '>', '0');

                    $this->excludeCanceledContractsQuery($query);
                },
                'modems as modems_critical_count' => function ($query) {
                    $query->where('us_pwr', '>=', config('hfccustomer.threshhold.single.us.critical'));

                    $this->excludeCanceledContractsQuery($query);
                },
            ]);
    }

    /**
     * This is an extension of scopeWithActiveModems's withCount() method to exclude all modems of canceled contracts
     * Note: This is not a scope and can not be used as one - this is because withCount method automatically joins named table (modem) and for a scope it would have to be joined here as well - join it twice doesnt work!
     *
     * @param  Illuminate\Database\Query\Builder  $query
     * @return Illuminate\Database\Query\Builder
     */
    private function excludeCanceledContractsQuery($query)
    {
        return $query
            // ->leftJoin('modem', 'modem.netelement_id', 'netelement.id')
            ->join('contract', 'contract.id', 'modem.contract_id')
            ->whereNull('modem.deleted_at')
            ->whereNull('contract.deleted_at')
            ->where('contract_start', '<=', date('Y-m-d'))
            ->where(whereLaterOrEqual('contract_end', date('Y-m-d')));
    }

    /**
     * Relations
     */
    public function modems()
    {
        return $this->hasMany(\Modules\ProvBase\Entities\Modem::class, 'netelement_id');
    }

    public function passive_modems()
    {
        return $this->hasMany(\Modules\ProvBase\Entities\Modem::class, 'next_passive_id');
    }

    public function geoPosModems()
    {
        return $this->hasMany(\Modules\ProvBase\Entities\Modem::class, 'netelement_id')
            ->select('modem.id', 'modem.x', 'modem.y', 'modem.netelement_id')
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw('COUNT(CASE WHEN `us_pwr` = 0 THEN 1 END) as offline')
            ->groupBy('modem.x', 'modem.y')
            ->havingRaw('max(us_pwr) > 0 AND min(us_pwr) = 0 AND count > 1')
            ->join('contract', 'contract.id', 'modem.contract_id')
            ->whereNull('modem.deleted_at')
            ->whereNull('contract.deleted_at')
            ->where('contract_start', '<=', date('Y-m-d'))
            ->where(whereLaterOrEqual('contract_end', date('Y-m-d')));
    }

    // Relation to MPRs Modem Positioning Rules
    public function mprs()
    {
        return $this->hasMany(\Modules\HfcCustomer\Entities\Mpr::class, 'netelement_id');
    }

    public function snmpvalues()
    {
        return $this->hasMany(\Modules\HfcSnmp\Entities\SnmpValue::class, 'netelement_id');
    }

    public function netelementtype()
    {
        return $this->belongsTo(NetElementType::class);
    }

    public function indices()
    {
        return $this->hasMany(\Modules\HfcSnmp\Entities\Indices::class, 'netelement_id');
    }

    public function apartment()
    {
        return $this->belongsTo(\Modules\PropertyManagement\Entities\Apartment::class);
    }

    /**
     * As Android and Iphone app developers use wrong columns to display object name, we use the relation
     * column to describe the object as well
     */
    public function icingaObject()
    {
        return $this
            ->hasOne(\Modules\HfcBase\Entities\IcingaObject::class, 'name1', 'id_name')
            ->where('objecttype_id', '1')
            ->where('is_active', '1');
    }

    public function getIcingaHostAttribute()
    {
        return optional($this->icingaObject)->icingahost;
    }

    /**
     * Relation to Objects that refer to IcingaServices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function icingaServices()
    {
        return $this
            ->hasManyThrough(
                \Modules\HfcBase\Entities\IcingaServiceStatus::class,
                \Modules\HfcBase\Entities\IcingaObject::class,
                'name1', // foreign key on IcingaObject
                'service_object_id', // Foreign key on IcingaServiceStatus
                'id_name', // Local Key on Netelement
                'object_id' // Local Key on IcingaObject
            )
            ->select('icinga_objects.name2')
            ->where('icinga_objects.is_active', 1);
    }

    public function getIcingaHostStatusAttribute()
    {
        if ($this->hostStatus) {
            return $this->hostStatus;
        }

        return $this->icingaObject->hostStatus;
    }

    /**
     * Depending on Netelement Base Type the relation is made
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provDevice()
    {
        if ($this->netelementtype->base_type == 3) {
            return $this->belongsTo(\Modules\ProvBase\Entities\NetGw::class);
        }

        return $this->belongsTo(\Modules\ProvBase\Entities\Modem::class);
    }

    /**
     * Link for this Host in IcingaWeb2
     *
     * @return string
     */
    public function toIcingaWeb()
    {
        if ($this->getRelation('icingaObject')) {
            return 'https://'.request()->server('HTTP_HOST').'/icingaweb2/monitoring/host/show?host='.$this->icingaObject->name1;
        }

        return 'https://'.request()->server('HTTP_HOST').'/icingaweb2/monitoring/host/show?host='.$this->id.'_'.$this->name;
    }

    /**
     * Link to Controlling page in NMS Prime, if this Host is registered as a
     * NetElement in NMS Prime.
     *
     * @return string|void
     */
    public function toControlling()
    {
        return route('NetElement.controlling_edit', [
            'id' => $this->id,
            'parameter' => 0,
            'index' => 0,
        ]);
    }

    /**
     * Link to ERD overview. Depending of the information available the
     * netelement.
     *
     * @return string
     */
    public function toErd()
    {
        if ($this->cluster) {
            return route('TreeErd.show', ['field' => 'cluster', 'search' => $this->cluster]);
        }

        if ($this->net) {
            return route('TreeErd.show', ['field' => 'net', 'search' => $this->net]);
        }

        return route('TreeErd.show', ['field' => 'id', 'search' => $this->id]);
    }

    /**
     * Url of netelement at ERD-Vicinity graph as model Attribute
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        $url = '';
        if ($this->link) {
            $url = $this->link;
        } elseif ($this->netelementtype_id == 8) {
            $url = route('TreeErd.show', ['parent_id', $this->id]);
        } elseif ($this->netelementtype_id == 9) {
            $url = Module::collections()->has('Satkabel') ? route('NetElement.tapControlling', $this->id) : '';
        } else {
            $url = route('NetElement.controlling_edit', [$this->id, 0, 0]);
        }

        return $url;
    }

    /**
     * Link to Ticket creation form already prefilled.
     *
     * @return string
     */
    public function toTicket()
    {
        if (! Module::collections()->has('Ticketsystem')) {
            return;
        }

        if ($this->icingaObject) {
            return route('Ticket.create', [
                'name' => e($this->icingaObject->name1),
                'netelement_id' => $this->id,
            ]);
        }

        return route('Ticket.create', [
            'name' => e($this->name),
            'netelement_id' => $this->id,
        ]);
    }

    /**
     * Tries to get the amount of affected modems of the related NetElement.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $netelements
     * @return int
     */
    public function affectedModemsCount($netelements)
    {
        return $netelements[$this->netelement->id] ?? 0;
    }

    public function clusterObj()
    {
        return $this->belongsTo(self::class, 'cluster');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function clusters()
    {
        $cluster_id = array_search('Cluster', NetElementType::$undeletables);

        return $this->hasMany(self::class, 'net')
            ->where('id', '!=', $this->id)
            ->where('netelementtype_id', $cluster_id)
            ->orderBy('name');
    }

    /**
     * Get the average upstream power of connected modems
     *
     * @return HasMany Filtered and aggregated modem Relationship
     */
    public function modemsUpstreamAvg()
    {
        return $this->modems()
            ->where('us_pwr', '>', '0')
            ->selectRaw('AVG(us_pwr) as us_pwr_avg, netelement_id')
            ->groupBy('netelement_id');
    }

    public function modemsUpstreamAndPositionAvg()
    {
        return $this->modems()
            ->where('us_pwr', '>', '0')
            ->where('x', '<>', '0')
            ->where('y', '<>', '0')
            ->selectRaw('AVG(us_pwr) as us_pwr_avg, AVG(x) as x_avg, AVG(y) as y_avg, netelement_id')
            ->groupBy('netelement_id');
    }

    /**
     * Laravel Magic Method to access average upstream power of connected modems
     *
     * @return int
     */
    public function getModemsUsPwrAvgAttribute()
    {
        if (array_key_exists('modemsUpstreamAndPositionAvg', $this->relations)) {
            return round(optional($this->getRelation('modemsUpstreamAndPositionAvg')->first())->us_pwr_avg, 1);
        }

        if (! array_key_exists('modemsUpstreamAvg', $this->relations)) {
            $this->load('modemsUpstreamAvg');
        }

        return round(optional($this->getRelation('modemsUpstreamAvg')->first())->us_pwr_avg, 1);
    }

    /**
     * Laravel Magic Method to access aggregated modem stats
     * here: upstream, x and y geopos
     *
     * @return int
     */
    public function getModemsUsPwrPosAvgsAttribute()
    {
        if (! array_key_exists('modemsUpstreamAndPositionAvg', $this->relations)) {
            $this->load('modemsUpstreamAndPositionAvg');
        }

        return optional($this->getRelation('modemsUpstreamAndPositionAvg')->first());
    }

    /**
     * Get first parent of type NetGw
     *
     * @return object NetElement    (or NULL if there is no parent NetGw)
     */
    public function get_parent_netgw()
    {
        $parent = $this;

        do {
            $parent = $parent->parent()->with('netelementtype')->first();

            if (! $parent) {
                break;
            }
        } while (! $parent->netelementtype || $parent->netelementtype->base_type != 3);

        return $parent;
    }

    /**
     * Get List of netelements for edit view select field
     *
     * @return array
     */

    /**
     * Format Parent (NetElements) for Select 2 field and allow for searching.
     *
     * @param  string|null  $search
     * @request param model The id of the model or null if in create context
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Parent(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        $modelId = request('model') ?? 0;

        return self::select('netelement.id')
            ->join('netelementtype as nt', 'nt.id', '=', 'netelementtype_id')
            ->selectRaw('CONCAT(nt.name,\': \', netelement.name) as text')
            ->where('netelement.id', '!=', $modelId)
            ->where(function ($query) use ($modelId) {
                $query
                    ->where('netelement.parent_id', '!=', $modelId)
                    ->orWhereNull('netelement.parent_id');
            })
            ->when($search, function ($query, $search) {
                return $query->where('netelement.name', 'like', "%{$search}%")
                    ->orWhere('nt.name', 'like', "%{$search}%");
            });
    }

    /**
     * Format Provisioning Device Connection for Select 2 field and allow for
     * searching. Depending on NetElemetType id the relation differs.
     *
     * @param  string|null  $search
     * @request param netelementtype_id The NetElemetType id in create context
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2ProvDevice(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        $class = 'Modules\\ProvBase\\Entities\\'.(request('base_type') == 3 ? 'NetGw' : 'Modem');

        return $class::select('id', 'hostname as text')
            ->when($search, function ($query, $search) {
                return $query->where('hostname', 'like', "%{$search}%");
            });
    }

    /**
     * Format Netelements for Select 2 field and allow for searching.
     *
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Netelements(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        return NetElement::select('netelement.id')
            ->join('netelementtype as nt', 'nt.id', '=', 'netelementtype_id')
            ->selectRaw('CONCAT(nt.name,\': \', netelement.name) as text')
            ->when($search, function ($query, $search) {
                return $query->where('netelement.name', 'like', "%{$search}%")
                    ->orWhere('nt.name', 'like', "%{$search}%");
            });
    }

    /**
     * Format NetElemetType for Select 2 field and allow for searching.
     *
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select2Netelementtypes(?string $search): \Illuminate\Database\Eloquent\Builder
    {
        return NetElementType::select('id', 'name as text', 'version as count')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('version', 'like', "%{$search}%");
            });
    }

    public function getApartmentsList()
    {
        $apartments = \Modules\PropertyManagement\Entities\Apartment::leftJoin('netelement as n', 'apartment.id', 'n.apartment_id')
            ->whereNull('n.deleted_at')
            ->where(function ($query) {
                $query
                    ->whereNull('n.id')
                    ->orWhere('apartment.id', $this->apartment_id);
            })
            ->select('apartment.*')
            ->get();

        $list[null] = null;

        foreach ($apartments as $apartment) {
            $list[$apartment->id] = \Modules\PropertyManagement\Entities\Apartment::labelFromData($apartment);
        }

        return $list;
    }

    // TODO: rename, avoid recursion
    public function get_non_location_parent($layer = '')
    {
        return $this->parent;

        $p = $this->parent;

        if ($p->type == 'LOCATION') {
            return get_non_location_parent($p);
        } else {
            return $p;
        }
    }

    /**
     * Return favorited Nets of the user or the first 25 Nets
     */
    public static function getSidebarNets()
    {
        return Cache::remember(Auth::user()->login_name.'-Nets', now()->addMinutes(5), function () {
            $nets = Auth::user()
                ->favNetelements()
                ->without('netelementtype')
                ->get(['netelement.id', 'name', 'netelementtype_id']);

            if ($nets->count()) {
                return $nets;
            }

            return self::where('netelementtype_id', array_search('Net', NetElementType::$undeletables))
                ->without('netelementtype')
                ->limit(25)
                ->get(['id', 'name', 'netelementtype_id']);
        });
    }

    /**
     * Returns all available firmware files (via directory listing)
     *
     * @author Patrick Reichel
     */
    public function kml_files()
    {
        // get all available files
        $kml_files_raw = glob(storage_path($this->kml_path.'/*'));
        $kml_files = [null => 'None'];
        // extract filename
        foreach ($kml_files_raw as $file) {
            if (is_file($file)) {
                $parts = explode('/', $file);
                $filename = array_pop($parts);
                $kml_files[$filename] = $filename;
            }
        }

        return $kml_files;
    }

    /*
     * Helpers from NMS
     */
    private function _get_native_helper($type = 'Net')
    {
        $p = $this;
        $i = 0;

        do {
            if (! is_object($p)) {
                return 0;
            }

            if ($p->{'is_type_'.strtolower($type)}()) {
                return $p->id;
            }

            $p = $p->parent;
        } while ($i++ < $this->max_parents);
    }

    public function get_native_cluster()
    {
        return $this->_get_native_helper('Cluster');
    }

    public function get_native_net()
    {
        return $this->_get_native_helper();
    }

    public function get_native_netgw()
    {
        return $this->_get_native_helper('NetGw');
    }

    // TODO: depracted, remove
    public function get_layer_level($layer = '')
    {
        return 0;
    }

    /**
     * Build net and cluster index for $this NetElement Objects - Currently not used
     */
    // public function relation_index_build ()
    // {
    // 	$this->net     = $this->get_native_net();
    // 	$this->cluster = $this->get_native_cluster();
    // }

    /**
     * Build net and cluster index for all NetElement Objects
     *
     * @params call_from_cmd: set if called from artisan cmd for state info
     */
    public static function relation_index_build_all($call_from_cmd = 0)
    {
        \Log::info('nms: build net and cluster index of all tree objects');

        $num = self::count();

        self::chunk(1000, function ($netelements) use ($num) {
            static $i = 1;

            foreach ($netelements as $netelement) {
                $netelement->update(['net' => $netelement->get_native_net(),
                    'cluster' => $netelement->get_native_cluster(),
                    'netgw_id' => $netelement->get_native_netgw(), ]);

                $debug = "nms: netelement - rebuild net and cluster index $i of $num - id ".$netelement->id;
                echo "\n$debug - net:".$netelement->net.', clu:'.$netelement->cluster.', netgw:'.$netelement->netgw_id;

                $i++;
            }
        });

        echo "\n";
    }

    /**
     * Check if NetElement is of Type Net (belongs to NetElementType with name 'Net')
     *
     * @return bool
     */
    public function is_type_net()
    {
        return $this->netelementtype_id == array_search('Net', NetElementType::$undeletables);
    }

    public function is_type_cluster()
    {
        return $this->netelementtype_id == array_search('Cluster', NetElementType::$undeletables);
    }

    public function is_type_netgw()
    {
        if (! $this->netelementtype) {
            return false;
        }

        return $this->netelementtype->base_type == 3; // 3 .. is base element for netgw
    }

    /**
     * Return the base NetElementType id
     *
     * @param
     * @return int [1: Net, 2: Cluster, 3: NetGw, 4: Amp, 5: Node, 6: Data]
     */
    public function get_base_netelementtype()
    {
        return $this->netelementtype->base_type;
    }

    /**
     * Return hard coded $this->options array
     * NOTE: this is of course type dependent
     *
     * @param
     * @return array()
     */
    public function get_options_array($type = null)
    {
        if (! $type) {
            $type = $this->netelementtype->base_type;
        }

        if ($type != 2) {  // cluster
            return [];
        }

        return [
            '0' => '8x4', // default
            '81' => '8x1',
            '82' => '8x2',
            '84' => '8x4',
            '88' => '8x8',
            '124' => '12x4',
            '128' => '12x8',
            '164' => '16x4',
            '168' => '16x8',
        ];
    }

    /**
     * Returns all tabs for the view depending on the NetelementType
     * Note: 1 = Net, 2 = Cluster, 3 = NetGw, 4 = Amplifier, 5 = Node, 6 = Data, 7 = UPS, 8 = Tap, 9 = Tap-Port, 10 = NMSPrime slave
     *
     * @return array
     *
     * @author Roy Schneider, Nino Ryschawy
     */
    public function tabs()
    {
        if (! $this->netelementtype) {
            return [];
        }

        $provmonEnabled = Module::collections()->has('ProvMon');
        $type = $this->netelementtype->base_type;

        $tabs = [['name' => 'Edit', 'icon' => 'pencil', 'route' => 'NetElement.edit', 'link' => $this->id]];

        $sqlCol = $this->netelementtype->name;
        $id = $this->id;
        if (! in_array($type, [1, 2])) {
            $sqlCol = $this->cluster ? 'Cluster' : 'Net';
            $id = $this->cluster ? $this->cluster : $this->net;
        }

        $tabs[] = ['name' => 'Entity Diagram', 'icon' => 'sitemap', 'route' => 'TreeErd.show', 'link' => [$sqlCol, $id]];
        $tabs[] = ['name' => 'Topography', 'icon' => 'map', 'route' => 'TreeTopo.show', 'link' => [$sqlCol, $id]];

        if (! Module::collections()->has('HfcBase')) {
            $tabs[array_key_last($tabs) - 1]['route'] = 'missingModule';
            $tabs[array_key_last($tabs) - 1]['link'] = 'HfcBase';
            $tabs[array_key_last($tabs)]['route'] = 'missingModule';
            $tabs[array_key_last($tabs)]['link'] = 'HfcBase';
        }

        if (! in_array($type, [1, 8, 9])) {
            $tabs[] = ['name' => 'Controlling', 'icon' => 'wrench', 'route' => 'NetElement.controlling_edit', 'link' => [$this->id, 0, 0]];
        }

        if ($type == 9) {
            $tabs[] = ['name' => 'Controlling', 'icon' => 'bar-chart fa-rotate-90', 'route' => 'NetElement.tapControlling', 'link' => [$this->id]];

            if (! \Module::collections()->has('Satkabel')) {
                $tabs[array_key_last($tabs)]['route'] = 'missingModule';
                $tabs[array_key_last($tabs)]['link'] = 'Satkabel';
            }
        }

        if ($type == 4 || $type == 5) {
            // Create Analysis tab (for ORA/VGP) if IP address is no valid IP
            $route = $provmonEnabled ? 'ProvMon.index' : 'Modem.analysis';
            $tabs[] = ['name' => trans('view.analysis'), 'icon' => 'area-chart', 'route' => $route, 'link' => $this->getModemIdFromHostname($this->ip)];
        }

        if (! in_array($type, [4, 5, 8, 9])) {
            $tabs[] = ['name' => 'Diagrams', 'icon' => 'area-chart', 'route' => 'ProvMon.diagram_edit', 'link' => [$this->id]];

            if (! $provmonEnabled && Module::collections()->has('HfcCustomer')) {
                $tabs[array_key_last($tabs)]['route'] = 'missingModule';
                $tabs[array_key_last($tabs)]['link'] = 'Prime Monitoring & Prime Detect';
            }
        }

        return $tabs;
    }

    /**
     * Return number from IP address field if the record is written like: 'cm-...'.
     *
     * @param string
     * @return string
     *
     * @author Roy Schneider
     */
    private function getModemIdFromHostname($hostname)
    {
        preg_match('/[c][m]\-\d+/', $hostname, $return);

        if (empty($return)) {
            return '0';
        }

        return substr($return[0], 3);
    }

    /**
     * Get the IP address if set, otherwise return IP address of parent NetGw
     *
     * @return string: IP address (null if not found)
     *
     * @author Ole Ernst
     */
    private function _get_ip()
    {
        if ($this->ip) {
            return $this->ip;
        }

        if (! $netgw = $this->get_parent_netgw()) {
            return;
        }

        return $netgw->ip ?: null;
    }

    /**
     * Apply automatic gain control for a cluster
     *
     * @author Ole Ernst
     */
    public function apply_agc()
    {
        // ignore non-clusters
        if ($this->netelementtype_id != 2) {
            return;
        }
        // ignore cluster if its IP address can't be determined
        if (! $ip = $this->_get_ip()) {
            return;
        }

        // get all docsIfUpstreamChannelTable indices of cluster
        $idxs = $this->indices
            ->filter(function ($idx) {
                return $idx->parameter->oid->oid == '.1.3.6.1.2.1.10.127.1.1.2';
            })->pluck('indices')
            ->map(function ($i) {
                return explode(',', $i);
            })->collapse();

        $com = $this->community_rw ?: \Modules\ProvBase\Entities\ProvBase::first()->rw_community;

        // retrieve numeric values only
        snmp_set_quick_print(true);

        echo "Cluster: $this->name\n";
        foreach ($idxs as $idx) {
            try {
                $snr = snmp2_get($ip, $com, ".1.3.6.1.2.1.10.127.1.1.4.1.5.$idx");
                if (! $snr) {
                    // continue if snr is zero (i.e. no CM on the channel)
                    continue;
                }
            } catch (\Exception $e) {
                \Log::error("Could not get SNR for cluster $this->name ($idx)");
                continue;
            }

            try {
                $rx = snmp2_get($ip, $com, ".1.3.6.1.4.1.4491.2.1.20.1.25.1.2.$idx");
            } catch (\Exception $e) {
                \Log::error("Could not get RX power for cluster $this->name ($idx)");
                continue;
            }

            $offset = $this->agc_offset;
            // the reference SNR is 24 dB
            $r = round($rx + 24 * 10 - $snr, 1) + $offset * 10;
            // minimum actual power is 0 dB
            if ($r < 0) {
                $r = ($offset < 0) ? 0 : $offset * 10;
            }
            // maximum actual power is 10 dB
            if ($r > 100) {
                $r = 100;
            }

            echo "$idx: $rx -> $r\t($snr)\n";
            try {
                snmp2_set($ip, $com, ".1.3.6.1.4.1.4491.2.1.20.1.25.1.2.$idx", 'i', $r);
            } catch (\Exception $e) {
                \Log::error("Error while setting new exptected us power for cluster $this->name ($idx: $r)");
            }
        }
    }

    /**
     * Collect the necessary data for TicketReceiver and Notifications.
     *
     * @return array
     */
    public function getTicketSummary()
    {
        if ($this->pos) {
            $pos = explode(',', $this->pos);

            $navi = [
                'link' => "https://www.google.com/maps/dir/my+location/{$pos[1]},{$pos[0]}",
                'icon' => 'fa-location-arrow',
                'title' => trans('messages.route'),
            ];
        }

        return [
            trans('messages.Device') => [
                'text' => "{$this->netelementtype->vendor} {$this->netelementtype->name} {$this->netelementtype->version}",
            ],
            trans('messages.name') => [
                'text' => $this->name,
            ],
            trans('messages.position') => [
                'text' => $this->pos,
                'action' => $navi ?? null,
            ],
            trans('messages.CLUSTER') => [
                'text' => $this->clusterObj()->without('netelementtype')->first()->name ?? $this->cluster,
                'action' => [
                    'link' => route('CustomerTopo.show', ['id', $this->id]),
                    'icon' => 'fa-map',
                    'title' => trans('view.ticket.viewTopography'),
                ],
            ],
        ];
    }

    /**
     * To reduce AJAX Payload, only this subset is loaded.
     *
     * @return array
     */
    public function reducedFields()
    {
        return ['id', 'netelementtype', 'name', 'pos', 'cluster'];
    }

    public function getSnmpValuesStoragePath($ext = '')
    {
        $path = storage_path('app/').self::SNMP_VALUES_STORAGE_REL_DIR.$this->id;

        if ($ext) {
            $path .= '-'.$ext;
        }

        return $path;
    }

    public function isReachableViaSnmp()
    {
        $comm = $this->community();

        // SysDescr
        try {
            snmpget($this->ip, $comm, '.1.3.6.1.2.1.1.1.0');
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Return the Community String for Read-Only or Read-Write Access
     *
     * @param   access  String  'ro' or 'rw'
     *
     * @author  Nino Ryschawy
     */
    public function community($access = 'ro')
    {
        $community = $this->{'community_'.$access};

        if (! $community) {
            $community = \Modules\HfcReq\Entities\HfcReq::get([$access.'_community'])->first()->{$access.'_community'};
        }

        if (! $community) {
            Log::error("community {$access} for Netelement $this->id is not set!");

            throw new SnmpAccessException(trans('messages.NoSnmpAccess', ['access' => $access, 'name' => $this->name]));
        }

        return $community;
    }
}
