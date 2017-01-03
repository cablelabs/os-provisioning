<?php

namespace Modules\HfcReq\Http\Controllers;

use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcSnmp\Entities\OID;

class NetElementTypeController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		$hidden  = in_array($model->name, ['Net', 'Cluster']);
		$parents = $model->html_list(NetElementType::all(), 'name', true);

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => $hidden ? ['readonly'] : []),
			array('form_type' => 'text', 'name' => 'vendor', 'description' => 'Vendor', 'hidden' => $hidden ? '1' : '0'),
			array('form_type' => 'text', 'name' => 'version', 'description' => 'Version', 'hidden' => $hidden ? '1' : '0'),
			array('form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Device Type', 'value' => $parents, 'hidden' => $hidden ? '1' : '0'),
			array('form_type' => 'text', 'name' => 'icon_name', 'description' => 'Icon'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}


	/**
	 * Assign OIDs to NetElementType - Store in pivot/intermediate-table
	 *
	 * @param 	$id 			integer 	device type
	 * @input 	$mibfile_id 	integer 	ID of MIB-File we want to attach the OIDs to the device type
	 */
	public function add_oid_from_mib($id)
	{
		if (($mibfile_id = \Request::input('mibfile_id')) == 0)
			return \Redirect::back();

		// generate list of OIDs and attach to device type (fastest method)
		$oids = OID::where('mibfile_id', '=', $mibfile_id)->get(['id'])->keyBy('id')->keys()->all();

		$devtype = NetElementType::findOrFail($id);
		$devtype->oids()->attach($oids);

		return \Redirect::route('NetElementType.edit', $devtype->id);
	}


	/**
	 * Return the View for Assigning existing OIDs to the NetElementType
	 */
	public function assign($id)
	{
		$view_var 		= NetElementType::findOrFail($id);
		$view_header 	= 'Attach single OIDs';
		$headline       = 'Headline';

		$mibs = \Modules\HfcSnmp\Entities\MibFile::select(['id', 'name', 'version'])->get();
		$mibs = isset($mibs[0]) ? $mibs[0]->html_list($mibs, 'name', true) : [];

		// exclude mibs that don't have OIDs ??
		// foreach ($mibs as $mib)
		// {
		// 	if ($mib->oids)
		// 		$mibs_e[$mib->id] = $mib->name;
		// }

		$oids 	  = [];
		$oids_raw = OID::get(['id', 'name', 'oid']);
		foreach ($oids_raw as $key => $oid)
			$oids[$oid->id] = $oid->name.' - '.$oid->oid; 

		return \View::make('hfcreq::NetElementType.assign', $this->compact_prep_view(compact('view_header', 'headline', 'view_var', 'oids', 'mibs')));
	}


	/**
	 * Attach single chosen OIDs (multiselect) to NetElementType - Store in pivot/intermediate-table
	 *
	 * @param 	$id 			integer 	device type
	 * @input 					array 		IDs of the OIDs we want to attach to the given device type transfered via HTTP POST/PUT
	 */
	public function attach($id)
	{
		$devtype = NetElementType::findOrFail($id);
		$devtype->oids()->attach(\Request::input('oid_id'));

		return \Redirect::route('NetElementType.edit', $devtype->id);
	}


	/**
	 * Detach an existing OID from the NetElementType
	 */
	public function detach($id)
	{
		$devtype = NetElementType::findOrFail($id);
		$devtype->oids()->detach(array_keys(\Request::input('ids')));

		return \Redirect::back();
	}

	/**
	 * Detach all attached OID from the NetElementType
	 */
	public function detach_all($id)
	{
		$devtype = NetElementType::findOrFail($id);
		$oids 	 = array_keys($devtype->oids->keyBy('id')->all());

		$devtype->oids()->detach($oids);

		return \Redirect::back();
	}


	public static $INDEX = 0;
	public static $I = 0;
	public static $colours = ['', 'text-warning', 'text-danger', 'text-info', 'text-success'];

	public static function make_tree_table()
	{
		$data = '';

		// tree with select fields
		// $data .= '<div id="jstree-checkable" class="jstree jstree-2 jstree-default jstree-checkbox-selection" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1" aria-busy="false" aria-selected="false">';
		// $data .= '<ul class="jstree-container-ul jstree-children jstree-wholerow-ul jstree-no-dots" role="group">';

		// default tree
		// $data = '<div id="jstree-default" class="jstree jstree-1 jstree-default" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1" aria-busy="false">';
		// $data .= '<ul class="jstree-children" role="group" style>';

		$data .= self::create_index_view_data(NetElementType::get_tree_list());

		// $data .= '</ul></div>';

		return $data;
	}


	/**
	 * writes whole index view data in string
	 *
	 * @param array with all NetElementTypes in hierarchical tree structure
	 *
	 * @author Nino Ryschawy
	 */
	public static function create_index_view_data($ordered_tree)
	{
		$data = '';

		foreach ($ordered_tree as $object)
		// foreach ($ordered_tree as $key => $object)
		{
			if (is_array($object))
			{
				self::$INDEX += 1;
				if (self::$INDEX == 1)
					self::$I--;

				// $data .= '<ul role="group" class="jstree-children" style>';
				$data .= self::create_index_view_data($object);
				// $data .= '</ul>';
			}
			else
				// $data .= self::_print_label_elem($object, isset($ordered_tree[$key+1]));
				$data .= self::_print_label_elem($object);

			if (self::$INDEX == 0)
				self::$I++;
		}

		self::$INDEX -= 1;
		$data .= strpos(substr($data, strlen($data)-8), '<br><br>') === false ? '<br>' : '';

		return $data;
	}

	/**
	 * writes whole index view label element data in string
	 *
	 * @param NetElementType or array with NetElementType(s) and arrays of NetElementTypes
	 *
	 * @author Nino Ryschawy
	 */
	// public static function _print_label_elem($object, $list = false)
	private static function _print_label_elem($object)
	{
		$cur_model_complete = get_class($object);
		$cur_model_parts = explode('\\', $cur_model_complete);
		$cur_model = array_pop($cur_model_parts);

		$data = '';

		// default tree
		// $data .= '<li role="treeitem" data-jstree="{&quot;opened&quot;:true, &quot;selected&quot;:true" aria-selected="false" aria-level="'.self::$INDEX.'" aria-labelledby="'.self::$I.'_anchor" aria-expanded="true" id="j'.self::$I.'" class="jstree-node jstree-open">';
		// 	$data .= $list ? '<i class="jstree-icon jstree-ocl" role="presentation"></i>' : '';

		// tree with select fields
		// $data .= '<li role="treeitem" aria-selected="false" aria-level="'.self::$INDEX.'" aria-labelledby="'.self::$I.'_anchor" id="j'.self::$I.'" class="jstree-node  jstree-leaf">';
		// 	$data .= '<div unselectable="on" role="presentation" class="jstree-wholerow">&nbsp;</div><i class="jstree-icon jstree-ocl" role="presentation"></i>';

		for ($cnt = 0; $cnt <=self::$INDEX; $cnt++)
			$data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

		$data .= \Form::checkbox('ids['.$object->id.']', 1, Null, null, ['style' => 'simple', 'disabled' => $object->index_delete_disabled ? 'disabled' : null]).'&nbsp;&nbsp;';
		$data .= \HTML::linkRoute($cur_model.'.edit', $object->view_index_label(), $object->id, ['class' => self::$colours[self::$I % count(self::$colours)]]);
		$data .= '<br>';

		// link for javascript tree
		// $data .= '<a class="jstree-anchor" href="'.route($cur_model.'.edit', $object->view_index_label(), $object->id).'" tabindex="-1" id="'.self::$I.'_anchor">';
		// $data .= '<i class="jstree-icon jstree-themeicon fa fa-folder text-warning fa-lg jstree-themeicon-custom" role="presentation"></i>';
		// $data .= $object->view_index_label().'</a>';
		// $data .= '</li>';

		return $data;
	}


}
