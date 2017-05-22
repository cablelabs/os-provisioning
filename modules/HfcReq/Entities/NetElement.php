<?php

namespace Modules\HfcReq\Entities;

class NetElement extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'netelement';


	public $kml_path = 'app/data/hfcbase/kml/static';
	private $max_parents = 25;

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'name' 			=> 'required|string',
			'ip' 			=> 'ip',
			'pos' 			=> 'geopos',
			'community_ro' 	=> 'regex:/(^[A-Za-z0-9]+$)+/',
			'community_rw' 	=> 'regex:/(^[A-Za-z0-9]+$)+/',
			'netelementtype_id'	=> 'required|exists:netelementtype,id|min:1'
		);
	}


	public static function boot()
	{
		parent::boot();

		NetElement::observe(new NetElementObserver);
	}


	/*
	 * View Specific Stuff
	 */

	// Eager load Models so that only one Database Request is made when accessing type property (name of relational model netelementtype)
	public function index_list()
	{
		$eager_loading_model = new NetElementType;

		return $this->/*orderBy('parent_id')->*/orderBy('id')->with($eager_loading_model->table)->get();
	}

	// Name of View
	public static function view_headline()
	{
		return 'NetElement';
	}

	// Relations
	public function view_has_many()
	{
		$ret = [];

		// if (\PPModule::is_active('ProvBase'))
		// {
		// 	$ret['Edit']['Modem']['class'] 	  = 'Modem';
		// 	$ret['Edit']['Modem']['relation'] = $this->modems;
		// }

		if (\PPModule::is_active('HfcCustomer'))
		{
			$ret['Edit']['Mpr']['class'] 	= 'Mpr';
			$ret['Edit']['Mpr']['relation'] = $this->mprs;
		}

		if (\PPModule::is_active('hfcsnmp'))
		{
			if ($this->netelementtype && $this->netelementtype->parameters && $this->netelementtype->parameters->all())
			{
				$ret['Edit']['Indices']['class'] 	= 'Indices';
				$ret['Edit']['Indices']['relation'] = $this->indices;
			}

			// see NetElementController@controlling_edit for Controlling Tab!
		}

		return $ret;
	}

	// link title in index view
	public function view_index_label()
	{
		$bsclass = 'success';

		if ($this->state == 'YELLOW')
			$bsclass = 'warning';
		if ($this->state == 'RED')
			$bsclass = 'danger';

		$type = $this->netelementtype ? $this->netelementtype->name : '';

		// TODO: complete list
		return ['index' => [$this->id, $type, $this->name, $this->ip, $this->state, $this->pos],
				'index_header' => ['ID', 'Type', 'Name', 'IP', 'State', 'Position'],
				'bsclass' => $bsclass,
				'header' => $this->id.' - '.$this->name];
	}

	public function view_belongs_to ()
	{
		return $this->netelementtype;
	}


	/**
	 * Relations
	 */
	public function modems()
	{
		if (\PPModule::is_active('ProvBase'))
			return $this->hasMany('Modules\ProvBase\Entities\Modem', 'netelement_id');

		return null;
	}

	// Relation to MPRs Modem Positioning Rules
	public function mprs()
	{
		if (\PPModule::is_active('HfcCustomer'))
			return $this->hasMany('Modules\HfcCustomer\Entities\Mpr', 'netelement_id');

		return null;
	}

	public function snmpvalues()
	{
		return \PPModule::is_active('HfcSnmp') ? $this->hasMany('Modules\HfcSnmp\Entities\SnmpValue', 'netelement_id') : null;
	}

	public function netelementtype()
	{
		return $this->belongsTo('Modules\HfcReq\Entities\NetElementType');
	}

	public function indices()
	{
		if (\PPModule::is_active('HfcSnmp'))
			return $this->hasMany('Modules\HfcSnmp\Entities\Indices', 'netelement_id');
	}




	public function get_parent ()
	{
		if (!$this->parent_id || $this->parent_id < 1)
			return 0;

		return NetElement::find($this->parent_id);
	}

	public function get_children ()
	{
		return NetElement::whereRaw('parent_id = '.$this->id)->get();
	}


	// TODO: rename, avoid recursion
	public function get_non_location_parent($layer='')
	{
		return $this->get_parent();


		$p = $this->get_parent();

		if ($p->type == 'LOCATION')
			return get_non_location_parent($p);
		else
			return $p;
	}


	/**
	 * Return all NetElements of NetElementType Net (name = 'Net')
	 */
	public static function get_all_net ()
	{
		$net_id = array_search('Net', NetElementType::$undeletables);

		return NetElement::where('netelementtype_id', '=', $net_id)->get();
   
		// return NetElement::where('type', '=', 'NET')->get();
	}

	/**
	 * Return all NetElements of NetElementType with name=Cluster belonging to a special NetElement of Type Net (NetElementType with name=Net)
	 */
	public function get_all_cluster_to_net ()
	{
		// return NetElement::where('net','=',$this->id)->get();

		$cluster_id = array_search('Cluster', NetElementType::$undeletables);
		return NetElement::where('netelementtype_id', '=', $cluster_id)->where('net','=',$this->id)->get();

		// return NetElement::where('type', '=', 'CLUSTER')->where('net','=',$this->id)->get();
	}


	/**
	 * Returns all available firmware files (via directory listing)
	 * @author Patrick Reichel
	 */
	public function kml_files()
	{
		// get all available files
		$kml_files_raw = glob(storage_path($this->kml_path.'/*'));
		$kml_files = array(null => "None");
		// extract filename
		foreach ($kml_files_raw as $file) {
			if (is_file($file)) {
				$parts = explode("/", $file);
				$filename = array_pop($parts);
				$kml_files[$filename] = $filename;
			}
		}
		return $kml_files;
	}


	/*
	 * Helpers from NMS
	 */
	private function _get_native_helper ($type = 'Net')
	{
		$p = $this;
		$i = 0;

		do
		{
			if (!is_object($p))
				return 0;

			if ($p->{'is_type_'.strtolower($type)}())
				return $p->id;

			$p = $p->get_parent();
		} while ($i++ < $this->max_parents);

	}

	public function get_native_cluster ()
	{
		return $this->_get_native_helper('Cluster');
	}

	public function get_native_net ()
	{
		return $this->_get_native_helper('Net');
	}

	// TODO: depracted, remove
	public function get_layer_level($layer='')
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
	public static function relation_index_build_all ($call_from_cmd = 0)
	{
		return;

		$netelements = NetElement::all();

		\Log::info('nms: build net and cluster index of all tree objects');

		$i = 1;
		$num = count ($netelements);

		foreach ($netelements as $netelement)
		{
			$debug = "nms: netelement - rebuild net and cluster index $i of $num - id ".$netelement->id;
			\Log::debug($debug);

			$netelement->update(['net' => $netelement->get_native_net(), 'cluster' => $netelement->get_native_cluster()]);

			if ($call_from_cmd == 1)
				echo "$debug\r"; $i++;

			if ($call_from_cmd == 2)
				echo "\n$debug - net:".$netelement->net.', clu:'.$netelement->cluster;

		}

		echo "\n";
	}


	/**
	 * Check if NetElement is of Type Net (belongs to NetElementType with name 'Net')
	 *
	 * @return Bool
	 */
	public function is_type_net()
	{
		return $this->netelementtype_id == array_search('Net', NetElementType::$undeletables);
	}


	public function is_type_cluster()
	{
		return $this->netelementtype_id == array_search('Cluster', NetElementType::$undeletables);
	}

}




class NetElementObserver
{
	public function created($netelement)
	{
		if (!$netelement->observer_enabled)
			return;

		// if ($netelement->is_type_cluster())
		// in created because otherwise netelement does not have an ID yet
		$netelement->net 	 = $netelement->get_native_net();
		$netelement->cluster = $netelement->get_native_cluster();
		$netelement->observer_enabled = false; 		// don't execute functions in updating again
		$netelement->save();
	}

	public function updating($netelement)
	{
		if (!$netelement->observer_enabled)
			return;

		$netelement->net 	 = $netelement->get_native_net();
		$netelement->cluster = $netelement->get_native_cluster();
	}
}