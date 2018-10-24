<?php

namespace Modules\HfcReq\Entities;

use Session;
use Modules\HfcBase\Entities\IcingaObjects;

class NetElement extends \BaseModel
{
    // Do not delete children (modem, mta, phonenmumber, etc.)!
    protected $delete_children = false;

    // The associated SQL table for this Model
    public $table = 'netelement';

    public $guarded = ['kml_file_upload'];

    public $kml_path = 'app/data/hfcbase/kml_static';
    private $max_parents = 25;

    public $snmpvalues = ['attributes' => [], 'original' => []];

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'name' 			=> 'required|string',
            // 'ip' 			=> 'ip', 		// also hostname is permitted and soon also mac
            'pos' 			=> 'geopos',
            'community_ro' 	=> 'regex:/(^[A-Za-z0-9]+$)+/',
            'community_rw' 	=> 'regex:/(^[A-Za-z0-9]+$)+/',
            'netelementtype_id'	=> 'required|exists:netelementtype,id,deleted_at,NULL|min:1',
        ];
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
            $ret['Edit']['Mpr']['class'] = 'Mpr';
            $ret['Edit']['Mpr']['relation'] = $this->mprs;
        }

        if (\Module::collections()->has('HfcSnmp')) {
            if ($this->netelementtype && ($this->netelementtype->id == 2 || $this->netelementtype->parameters()->count())) {
                $ret['Edit']['Indices']['class'] = 'Indices';
                $ret['Edit']['Indices']['relation'] = $this->indices;
            }

            // see NetElementController@controlling_edit for Controlling Tab!
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
        if (in_array($this->get_elementtype_name(), ['Net', 'Cluster'])) {
            return 'info';
        }

        if (! IcingaObjects::db_exists()) {
            return 'warning';
        }

        $tmp = $this->icingaobjects;
        if ($tmp && $tmp->is_active) {
            $tmp = $tmp->icingahoststatus;
            if ($tmp) {
                return $tmp->last_hard_state ? 'danger' : 'success';
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
        return $this->netelementtype;
    }

    /**
     * Relations
     */
    public function modems()
    {
        return $this->hasMany('Modules\ProvBase\Entities\Modem', 'netelement_id');
    }

    // Relation to MPRs Modem Positioning Rules
    public function mprs()
    {
        return $this->hasMany('Modules\HfcCustomer\Entities\Mpr', 'netelement_id');
    }

    public function snmpvalues()
    {
        return $this->hasMany('Modules\HfcSnmp\Entities\SnmpValue', 'netelement_id');
    }

    public function netelementtype()
    {
        return $this->belongsTo('Modules\HfcReq\Entities\NetElementType');
    }

    public function indices()
    {
        return $this->hasMany('Modules\HfcSnmp\Entities\Indices', 'netelement_id');
    }

    public function icingaobjects()
    {
        return $this->hasOne('Modules\HfcBase\Entities\IcingaObjects', 'name1')->where('objecttype_id', '=', '1');
    }

    public function parent()
    {
        return $this->belongsTo('Modules\HfcReq\Entities\NetElement', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('Modules\HfcReq\Entities\NetElement', 'parent_id');
    }

    /**
     * Get first parent being a CMTS
     *
     * @return object NetElement 	(or NULL if there is no parent CMTS)
     */
    public function get_parent_cmts()
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
    public static function get_all_net()
    {
        $net_id = array_search('Net', NetElementType::$undeletables);

        return self::where('netelementtype_id', '=', $net_id)->get();

        // return NetElement::where('type', '=', 'NET')->get();
    }

    /**
     * Return all NetElements of NetElementType with name=Cluster belonging to a special NetElement of Type Net (NetElementType with name=Net)
     */
    public function get_all_cluster_to_net()
    {
        if (Session::has('Net-'.$this->id)) {
            return Session::get('Net-'.$this->id);
        }

        $cluster_id = array_search('Cluster', NetElementType::$undeletables);
        $return = self::where('netelementtype_id', '=', $cluster_id)->where('net', '=', $this->id)->orderBy('name')->get();

        Session::put('Net-'.$this->id, $return);

        return $return;
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

    public function get_native_cmts()
    {
        return $this->_get_native_helper('Cmts');
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
                                 'cmts' => $netelement->get_native_cmts(), ]);

            if ($call_from_cmd == 1) {
                echo "$debug\r";
            }
            $i++;

            if ($call_from_cmd == 2) {
                echo "\n$debug - net:".$netelement->net.', clu:'.$netelement->cluster.', cmts:'.$netelement->cmts;
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

    public function is_type_cmts()
    {
        if (! $this->netelementtype) {
            return false;
        }

        return $this->netelementtype->get_base_type() == 3; // 3 .. is base element for cmts
    }

    /**
     * Return the base NetElementType id
     *
     * @param
     * @return int [1: Net, 2: Cluster, 3: Cmts, 4: Amp, 5: Node, 6: Data]
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
}

class NetElementObserver
{
    public function created($netelement)
    {
        if (! $netelement->observer_enabled) {
            return;
        }

        $this->handleSidebarClusters($netelement);

        // if ($netelement->is_type_cluster())
        // in created because otherwise netelement does not have an ID yet
        $netelement->net = $netelement->get_native_net();
        $netelement->cluster = $netelement->get_native_cluster();
        $netelement->observer_enabled = false; 		// don't execute functions in updating again
        $netelement->save();
    }

    public function updating($netelement)
    {
        if (! $netelement->observer_enabled) {
            return;
        }

        if ($netelement['original']['parent_id'] != $netelement['attributes']['parent_id']) {
            $netelement->net = $netelement->get_native_net();
            $netelement->cluster = $netelement->get_native_cluster();

            // Change Net & cluster of all childrens too
            Netelement::where('parent_id', '=', $netelement->id)->update(['net' => $netelement->net, 'cluster' => $netelement->cluster]);

            $this->handleSidebarClusters($netelement, 1);
        }

        // if netelementtype_id changes -> indices have to change there parameter id - otherwise they are not used anymore
        if ($netelement['original']['netelementtype_id'] != $netelement['attributes']['netelementtype_id']) {
            $new_params = $netelement->netelementtype->parameters;

            foreach ($netelement->indices as $indices) {
                // assign each indices of parameter to new parameter with same oid
                if ($new_params->contains('oid_id', $indices->parameter->oid->id)) {
                    $indices->parameter_id = $new_params->where('oid_id', $indices->parameter->oid->id)->first()->id;
                    $indices->save();
                } else {
                    // Show Alert that not all indices could be assigned to the new parameter -> user has to create new indices and delete the old ones
                    // We also could delete them directly, so that user has to add them again
                    Session::put('info', trans('messages.indices_unassigned'));
                }
            }
        }
    }

    public function deleted($netelement)
    {
        $this->handleSidebarClusters($netelement);
    }

    protected function handleSidebarClusters($netelement, $isUpdating = 0)
    {
        if (! $netelement->is_type_cluster()) {
            return;
        }

        $netId = $netelement->get_native_net();

        if ($isUpdating) {
            $oldNet = NetElement::find($netelement['original']['parent_id']);
            $net = NetElement::find($netelement['attributes']['parent_id']);
            $oldNetId = $oldNet ? $oldNet->get_native_net() : 0;
            $netId = $net ? $net->get_native_net() : 0;

            $oldNetId ? Session::forget('Net-'.$oldNetId) : '';
        }

        $netId ? Session::forget('Net-'.$netId) : Session::forget('Net-'.$netelement->id);
    }
}
