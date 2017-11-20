<?php

namespace Modules\HfcReq\Entities;

use Modules\HfcSnmp\Entities\OID;
use Modules\HfcSnmp\Entities\Parameter;

class NetElementType extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'netelementtype';

	private $max_parents = 15;


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

	// View Icon
  public static function view_icon()
  {
    return '<i class="fa fa-object-group"></i>'; 
  }

	// link title in index view
	public function view_index_label()
	{
		// in Tree View returning an array is currently not yet implemented
		$version = $this->version ? ' - '.$this->version : '';
		return $this->name.$version;

		// return ['index' => [$this->name],
		//         'index_header' => ['Name'],
		//         'header' => $this->name];
	}

	public function view_index_label_ajax()
	{ 
		return $this;
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
			// $ret['Base']['Parameter']['class'] 	= 'Parameter';
			// $ret['Base']['Parameter']['relation']	= $this->parameters;

			// Extra view for easier attachment (e.g. attach all oids from one mibfile)
			$ret['Base']['Parameters']['view']['view'] = 'hfcreq::NetElementType.parameters';
			$ret['Base']['Parameters']['view']['vars']['list'] = $this->parameters ? : [];
			// Extra view for easier controlling view structure setting (html_frame, html_id of parameter)
			$ret['Parameter Settings']['Settings']['view']['view'] = 'hfcreq::NetElementType.settings';
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

	public function parameters()
	{
		return $this->hasMany('Modules\HfcSnmp\Entities\Parameter', 'netelementtype_id')->orderBy('html_frame')->orderBy('html_id')->orderBy('oid_id')->orderBy('id');
		// return $this->hasMany('Modules\HfcSnmp\Entities\Parameter', 'netelementtype_id')->orderBy('oid_id')->orderBy('id');
	}

	// only for preconfiguration of special device types (e.g. kathreins vgp)
	public function oid()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\OID', 'pre_conf_oid_id');
	}

	public function get_parent ()
	{
		if (!$this->parent_id || $this->parent_id < 1)
			return 0;

		return NetElementType::find($this->parent_id);
	}

	public static function param_list($id)
	{
		$eager_loading_model = new OID;
		$params = Parameter::where('netelementtype_id', '=', $id)->with($eager_loading_model->table)->get();

		if (!$params)
			return [];

		foreach ($params as $param)
			$list[$param->id] = $param->oid->gui_name ? $param->oid->oid.' - '.$param->oid->gui_name : $param->oid->oid.' - '.$param->oid->name;

		return $list;
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
		$netelementtypes = NetElementType::orderBy('parent_id')->orderBy('id')->get();
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


	/**
	 * Return the base type id of the current NetElementType
	 *
	 * @param
	 * @return integer [1: Net, 2: Cluster, 3: Cmts, 4: Amp, 5: Node, 6: Data]
	 */
	public function get_base_type()
	{
		$p = $this;
		$i = 0;

		do
		{
			if (!is_object($p))
				return false;

			if ($p->id >=1 && $p->id <= 6)
				return $p->id;

			$p = $p->get_parent();
		} while ($i++ < $this->max_parents);

		return false;
	}

}
