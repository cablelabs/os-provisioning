<?php

namespace Modules\HfcReq\Entities;

class NetElementType extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'netelementtype';


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'name' => 'required',
		);
	}


	/**
	 * View Stuff
	 */

	// Name of View
	public static function view_headline()
	{
		return 'NetElementType';
	}

	// link title in index view
	public function view_index_label()
	{
		// in Tree View returning an array is currently not yet implemented
		return $this->name;

		// return ['index' => [$this->name],
		//         'index_header' => ['Name'],
		//         'header' => $this->name];
	}

	public function index_list ()
	{
		// implement Index View as Tree - make sure that a separate index.blade.php is installed that includes the Generic.tree blade
		// so we can use the Generic BaseController@index function
		return NetElementType::get_tree_list();

		// $types = $this->orderBy('id')->get();
		// $undeletables = ['Net', 'Cluster'];

		// foreach ($types as $type)
		// {
		// 	if (in_array($type->name, $undeletables))
		// 		$type->index_delete_disabled = true;
		// }

		// return $types;
	}

	// returns all objects that are related to a DeviceType
	public function view_has_many()
	{
		$ret['Base']['NetElement']['class'] 	= 'NetElement';
		$ret['Base']['NetElement']['relation']  = $this->netelements;

		if (\PPModule::is_active('hfcsnmp') && !in_array($this->name, self::$undeletables))
		{
			// extra page or on Base ??
			// $ret['Base']['- Assign OIDs from MIB']['view']['view'] = 'hfcreq::netelementtype.add_oid_from_mib';
			// $mibs = \Modules\HfcSnmp\Entities\MibFile::select(['id', 'name', 'version'])->get();
			// $ret['Base']['- Assign OIDs from MIB']['view']['vars']['list'] = isset($mibs[0]) ? $mibs[0]->html_list($mibs, 'name', true) : [];

			// $ret['Base']['OID']['class'] 	= 'OID';
			// $ret['Base']['OID']['relation'] = $this->oids;
			// $ret['Base']['OID']['options']['hide_delete_button'] = 0;
			// $ret['Base']['OID']['options']['hide_create_button'] = 0;
			$ret['Base']['OIDs']['view']['view'] = 'hfcreq::NetElementType.oids';
			$ret['Base']['OIDs']['view']['vars']['list'] = $this->oids;

		}

		return $ret;
	}

	/**
	 * Relations
	 */
	public function netelements()
	{
		return $this->hasMany('Modules\HfcReq\Entities\NetElement', 'netelementtype_id');
	}

	public function oids()
	{
		return $this->belongsToMany('Modules\HfcSnmp\Entities\OID', 'netelementtype_oid', 'netelementtype_id', 'oid_id')->orderBy('oid');
	}




	/**
	 * These Types are relevant for whole Entity Relation Diagram and therefore must not be deleted
	 * Furthermore they are ordered by there Database ID which is probably used as fix value in many places of the source code
	 * So don't change this order unless you definitly know what you are doing !!!
	 */
	public static $undeletables = [1 => 'Net', 2 => 'Cluster'];


	/**
	 * Get all Database Entries with relevant data for index view ordered
	 *
	 * TODO: use in generic manner in BaseModel - note the undeletables array in other models!
	 *
	 * @return 	Multidimensional Array
	 */
	public static function get_tree_list()
	{
		$netelementtypes = NetElementType::orderBy('parent_id')->orderBy('id')->get(['id', 'parent_id', 'name']);
		$types = [];

		foreach ($netelementtypes as $key => $elem)
		{
			if ($elem->parent_id)
				break;

			if (in_array($elem->name, self::$undeletables))
				$elem->index_delete_disabled = true;

			$types[]  = $elem;
			unset($netelementtypes[$key]); 		// increases performance a bit

			$children = $elem->_get_children($netelementtypes);
			if ($children)
				$types[] = $children;
		}

		return $types;
	}


	/**
	 * Search Children from Collection List of NetElementTypes recursivly
	 *
	 * @param 	Collection $objects
	 * @return 	Array 
	 */
	private function _get_children($objects = null)
	{
		$children = $objects ? $objects->where('parent_id', $this->id) : [];
		$arr = [];

		foreach ($children as $key => $elem)
		{
			if (in_array($elem->name, self::$undeletables))
				$elem->index_delete_disabled = true;

			$arr[] = $elem;
			$tmp   = $elem->_get_children($objects);
			if ($tmp)
				$arr[] = $tmp;
		}

		return $arr;
	}

}