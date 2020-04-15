<?php

namespace Modules\HfcReq\Entities;

use Auth;
use Cache;
use Request;
use Session;
use Modules\HfcBase\Entities\IcingaObject;

class NetElement extends \BaseModel
{
    // Do not delete children (modem, mta, phonenmumber, etc.)!
    protected $delete_children = false;

    // The associated SQL table for this Model
    public $table = 'netelement';
    // Always get netelementtype with it to reduce DB queries as it's very probable that netelementtype is queried
    protected $with = ['netelementtype'];

    public $guarded = ['kml_file_upload'];

    public $kml_path = 'app/data/hfcbase/kml_static';
    private $max_parents = 50;

    public $snmpvalues = ['attributes' => [], 'original' => []];

    // Add your validation rules here
    public static function rules($id = null)
    {
        $rules = [
            'name' 			=> 'required|string',
            'pos' 			=> 'nullable|geopos',
            'community_ro' 	=> 'nullable|regex:/(^[A-Za-z0-9]+$)+/',
            'community_rw' 	=> 'nullable|regex:/(^[A-Za-z0-9]+$)+/',
            'netelementtype_id'	=> 'required|exists:netelementtype,id,deleted_at,NULL|min:1',
            'agc_offset'	=> 'nullable|numeric|between:-99.9,99.9',
        ];

        if (Request::get('netelementtype_id') == 9) {
            // address1 for Tap-Ports is required and must be unique
            $rules['address1'] = 'required|unique:netelement,address1,'.$id.',id,deleted_at,NULL,netelementtype_id,9|regex:/^([0-9A-F]{2}){4}(~\d)*$/';
        }

        return $rules;
    }

    public static function boot()
    {
        parent::boot();

        self::observe(new NetElementObserver);
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

        // if (\Module::collections()->has('ProvBase'))
        // {
        // 	$ret['Edit']['Modem']['class'] 	  = 'Modem';
        // 	$ret['Edit']['Modem']['relation'] = $this->modems;
        // }

        if (\Module::collections()->has('HfcCustomer')) {
            if ($this->netelementtype->get_base_type() != 9) {
                $ret['Edit']['Mpr']['class'] = 'Mpr';
                $ret['Edit']['Mpr']['relation'] = $this->mprs;
            }
        }

        if (\Module::collections()->has('HfcSnmp')) {
            if ($this->netelementtype && ($this->netelementtype->id == 2 || $this->netelementtype->parameters()->count())) {
                $ret['Edit']['Indices']['class'] = 'Indices';
                $ret['Edit']['Indices']['relation'] = $this->indices;
            }

            // see NetElementController@controlling_edit for Controlling Tab!
        }

        if ($this->netelementtype->get_base_type() == 8) {
            $ret['Edit']['SubNetElement']['class'] = 'NetElement';
            $ret['Edit']['SubNetElement']['relation'] = $this->children;
        }

        return $ret;
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
            'index_header' => [$this->table.'.id', 'netelementtype.name', $this->table.'.name',  $this->table.'.ip', $this->table.'.pos', $this->table.'.options'],
            'header' =>  $this->id.' - '.$this->name,
            'bsclass' => $bsclass,
            'order_by' => ['0' => 'asc'],
            'eager_loading' => ['netelementtype'],
            'edit' => ['netelementtype.name' => 'get_elementtype_name'], ];
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

        if (! array_key_exists('icingaobject', $this->relations) && ! IcingaObject::db_exists()) {
            return 'warning';
        }

        $icingaObj = $this->icingaobject;
        if ($icingaObj && $icingaObj->is_active) {
            $icingaObj = $icingaObj->icingahoststatus;
            if ($icingaObj) {
                return $icingaObj->last_hard_state ? 'danger' : 'success';
            }
        }

        return 'warning';
    }

    //for empty relationships
    public function get_elementtype_name()
    {
        return $this->netelementtype ? $this->netelementtype->name : '';
    }

    public function view_belongs_to()
    {
        $ret = new \Illuminate\Database\Eloquent\Collection([$this->netelementtype]);

        if ($this->apartment_id) {
            $ret->add($this->apartment);
        }

        if ($this->parent_id) {
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
     * @param Illuminate\Database\Query\Builder $query
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeWithActiveModems($query, $field = 'id', $operator = '>', $id = 2)
    {
        $ModemHelper = \Modules\HfcCustomer\Entities\ModemHelper::class;

        return $query->where($field, $operator, $id)
        ->orderBy('pos')
        ->withCount([
            'modems',
            'modems as modems_online_count' => function ($query) {
                $query->where('us_pwr', '>', '0');
            },
            'modems as modems_critical_count' => function ($query) use ($ModemHelper) {
                $query->where('us_pwr', '>', $ModemHelper::$single_critical_us);
            },
        ]);
    }

    /**
     * Relations
     */
    public function modems()
    {
        return $this->hasMany(\Modules\ProvBase\Entities\Modem::class, 'netelement_id');
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
    public function icingaobject()
    {
        return $this
            ->hasOne(\Modules\HfcBase\Entities\IcingaObject::class, 'name1', 'id_name')
            ->where('icinga_objects.objecttype_id', '1')
            ->where('icinga_objects.is_active', '1');
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
            ->where('netelementtype_id', $cluster_id);
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
     * @return object NetElement 	(or NULL if there is no parent NetGw)
     */
    public function get_parent_netgw()
    {
        $parent = $this;

        do {
            $parent = $parent->parent()->with('netelementtype')->first();

            if (! $parent) {
                break;
            }
        } while (! $parent->netelementtype || $parent->netelementtype->get_base_type() != 3);

        return $parent;
    }

    /**
     * Get List of netelements for edit view select field
     *
     * @return array
     */
    public function getParentList()
    {
        $netelems = \DB::table('netelement')->join('netelementtype as nt', 'nt.id', '=', 'netelementtype_id')
            ->select(['netelement.id as id', 'netelement.name as name', 'nt.name as ntname'])
            ->whereNull('netelement.deleted_at')
            ->get();

        return $this->html_list($netelems, ['ntname', 'name'], true, ': ');
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
     * Return all NetElements of NetElementType Net (name = 'Net')
     */
    public static function getNetsWithClusters()
    {
        return Cache::remember(Auth::user()->login_name.'-Nets', 5, function () {
            $net_id = array_search('Net', NetElementType::$undeletables);

            return self::where('netelementtype_id', '=', $net_id)
                ->with('clusters')
                ->get();
        });
    }

    /**
     * Returns all available firmware files (via directory listing)
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
        $netelements = self::all();

        \Log::info('nms: build net and cluster index of all tree objects');

        $i = 1;
        $num = count($netelements);

        foreach ($netelements as $netelement) {
            $debug = "nms: netelement - rebuild net and cluster index $i of $num - id ".$netelement->id;
            \Log::debug($debug);

            $netelement->update(['net' => $netelement->get_native_net(),
                'cluster' => $netelement->get_native_cluster(),
                'netgw_id' => $netelement->get_native_netgw(), ]);

            if ($call_from_cmd == 1) {
                echo "$debug\r";
            }
            $i++;

            if ($call_from_cmd == 2) {
                echo "\n$debug - net:".$netelement->net.', clu:'.$netelement->cluster.', netgw:'.$netelement->netgw_id;
            }
        }

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

        return $this->netelementtype->get_base_type() == 3; // 3 .. is base element for netgw
    }

    /**
     * Return the base NetElementType id
     *
     * @param
     * @return int [1: Net, 2: Cluster, 3: NetGw, 4: Amp, 5: Node, 6: Data]
     */
    public function get_base_netelementtype()
    {
        return $this->netelementtype->get_base_type();
    }

    /**
     * Return hard coded $this->options array
     * NOTE: this is of course type dependent
     *
     * @param
     * @return array()
     */
    public function get_options_array()
    {
        if ($this->get_base_netelementtype() == 2) { // cluster
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

        return [];
    }

    /**
     * Get the IP address if set, otherwise return IP address of parent NetGw
     *
     * @author Ole Ernst
     *
     * @return string: IP address (null if not found)
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
}

class NetElementObserver
{
    public function created($netelement)
    {
        if (! $netelement->observer_enabled) {
            return;
        }

        $this->flushSidebarNetCache();

        // if ($netelement->is_type_cluster())
        // in created because otherwise netelement does not have an ID yet
        $netelement->net = $netelement->get_native_net();
        $netelement->cluster = $netelement->get_native_cluster();
        $this->checkNetCluster($netelement);

        $netelement->observer_enabled = false;  // don't execute functions in updating again
        $netelement->save();
    }

    public function updating($netelement)
    {
        if (! $netelement->observer_enabled) {
            return;
        }

        if ($netelement->isDirty('parent_id', 'name')) {
            $this->flushSidebarNetCache();

            $netelement->net = $netelement->get_native_net();
            $netelement->cluster = $netelement->get_native_cluster();
            $this->checkNetCluster($netelement);

            // Change Net & cluster of all childrens too
            Netelement::where('parent_id', '=', $netelement->id)
                ->update([
                    'net' => $netelement->net,
                    'cluster' => $netelement->cluster,
                ]);
        }

        // if netelementtype_id changes -> indices have to change there parameter id
        // otherwise they are not used anymore
        if ($netelement->isDirty('netelementtype_id')) {
            $new_params = $netelement->netelementtype->parameters;

            foreach ($netelement->indices as $indices) {
                // assign each indices of parameter to new parameter with same oid
                if ($new_params->contains('oid_id', $indices->parameter->oid->id)) {
                    $indices->parameter_id = $new_params->where('oid_id', $indices->parameter->oid->id)->first()->id;
                    $indices->save();
                } else {
                    // Show Alert that not all indices could be assigned to the new parameter -> user has to create new indices and delete the old ones
                    // We also could delete them directly, so that user has to add them again
                    Session::put('alert.info', trans('messages.indices_unassigned'));
                }
            }
        }
    }

    public function deleted()
    {
        $this->flushSidebarNetCache();
    }

    protected function flushSidebarNetCache()
    {
        Cache::forget(Auth::user()->login_name.'-Nets');
    }

    /**
     * Return error message when net or cluster ID couldn't be determined as this would result in hiding the element in the ERD
     */
    private function checkNetCluster($netelement)
    {
        if (! $netelement->net) {
            return Session::push('tmp_error_above_form', trans('hfcreq::messages.netelement.noNet'));
        }

        if (! $netelement->net) {
            return Session::push('tmp_error_above_form', trans('hfcreq::messages.netelement.noCluster'));
        }
    }
}
