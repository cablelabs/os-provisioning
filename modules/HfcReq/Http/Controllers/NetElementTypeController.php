<?php

namespace Modules\HfcReq\Http\Controllers;

use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcSnmp\Entities\OID;
use Modules\HfcSnmp\Entities\Parameter;
use \App\Http\Controllers\BaseViewController;


class NetElementTypeController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		$hidden  = in_array($model->name, ['Net', 'Cluster']);
		$parents = $model->html_list(NetElementType::whereNotIn('name', ['Net', 'Cluster'])->get(['id', 'name']), 'name', true);

		// label(name) has to be the same like column in sql table
		$a = array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => $hidden ? ['readonly'] : []),
			array('form_type' => 'text', 'name' => 'vendor', 'description' => 'Vendor', 'hidden' => $hidden ? '1' : '0'),
			array('form_type' => 'text', 'name' => 'version', 'description' => 'Version', 'hidden' => $hidden ? '1' : '0'),
			array('form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Device Type', 'value' => $parents, 'hidden' => $hidden ? '1' : '0', 'space' => 1),
			// possibly load only OIDs from Mibs that are related to this Device/NetElement-Type
			array('form_type' => 'select', 'name' => 'pre_conf_oid_id', 'description' => 'OID for PreConfiguration Setting', 'value' => OID::oid_list(true)),
			array('form_type' => 'text', 'name' => 'pre_conf_value', 'description' => 'PreConfiguration Value'),
			array('form_type' => 'text', 'name' => 'pre_conf_time_offset', 'description' => 'PreConfiguration Time Offset', 'space' => 1),
			array('form_type' => 'text', 'name' => 'icon_name', 'description' => 'Icon'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);

		if ($hidden)
			$a[0]['help'] = trans('helper.undeleteables');

		return $a;
	}


	/**
	 * Return the View for Assigning existing OIDs to the NetElementType
	 *
	 * @return  View
	 */
	public function assign($id)
	{
		$view_var 		= NetElementType::findOrFail($id);
		$view_header 	= 'Attach single OIDs';
		$headline 		= BaseViewController::compute_headline(\NamespaceController::get_route_name(), $view_header, $view_var).' assign';

		// Get Mibs in case all OIDs from one Mib shall be attached
		$mibs = \Modules\HfcSnmp\Entities\MibFile::select(['id', 'name', 'version'])->get();
		$mibs = isset($mibs[0]) ? $mibs[0]->html_list($mibs, 'name', true) : [];

		// exclude mibs that don't have OIDs ??
		// foreach ($mibs as $mib)
		// {
		// 	if ($mib->oids)
		// 		$mibs_e[$mib->id] = $mib->name;
		// }

		// Get OIDs to Multiselect from
		$oids 	  = [];
		$oids_raw = OID::get(['id', 'name', 'oid']);
		foreach ($oids_raw as $key => $oid)
			$oids[$oid->id] = $oid->name.' - '.$oid->oid; 

		return \View::make('hfcreq::NetElementType.assign', $this->compact_prep_view(compact('view_header', 'headline', 'view_var', 'oids', 'mibs')));
	}


	/**
	 * Attach OIDs to a NetElementType - Store in pivot/intermediate-table (parameter) - Selection is done in assign.blade.php
	 * 
	 * Possible Methods:
	 	* Single Chosen OIDs via Multiselect
	 	* All OIDs from an already uploaded MibFile
	 	* A Newline-separated List of OIDs that have to exist in Database (from already uploaded MibFile)
	 *
	 * @param 	$id 			integer 	netelementtype id
	 * @input 	oid_id			array 		IDs of the OIDs we want to attach (transfered via HTTP POST)
	 * @input 	mibfile_id 		integer 	ID of MIB-File
	 * @input 	oid_list 		Text 		Newline-separated List of OIDs
	 *
	 * @author Nino Ryschawy
	 */
	public function attach_oids($id)
	{
		// Selected MibFile
		if (\Request::has('mibfile_id'))
		{
			if (($mibfile_id = \Request::input('mibfile_id')) == 0)
				return \Redirect::back();

			// generate list of OIDs and attach to device type (fastest method)
			$oids = OID::where('mibfile_id', '=', $mibfile_id)->get(['id'])->keyBy('id')->keys()->all();
		}

		// List from Textarea
		if (\Request::has('oid_list'))
		{
			$delimiters = [',', ';', "\n"];
			$oid_list = str_replace($delimiters, $delimiters[0], \Request::input('oid_list'));
			$oid_list = explode($delimiters[0], $oid_list);

			foreach ($oid_list as $oid)
			{
				$oid 	= trim($oid, "\r.0");
				$oid_o  = OID::where('oid', 'like', '%'.$oid)->get(['id'])->first();
				if ($oid_o)
					$oids[] = $oid_o->id;
			}
		}

		// Multiselect
		if (\Request::has('oid_id'))
		{
			$oids = \Request::input('oid_id');
		}

		// $devtype = NetElementType::findOrFail($id);
		// $devtype->oids()->attach($oids);

		$this->_create_parameter($id, $oids);

		// TODO: Implement Validation ?

		return \Redirect::route('NetElementType.edit', $id);
	}


	/**
	 * Creates a Parameter related to NetElementType for each OID in the List
	 *
	 * @param 	id 		Integer 	NetElementType ID
	 * @param 	oids 	Array 		List of OID IDs [0 => id1, 1 => id2, ...]
	 */
	private function _create_parameter($id, $oids)
	{
		foreach ($oids as $oid_id)
		{
			$data = array(
				'oid_id' => $oid_id,
				'netelementtype_id' => $id,
				);

			Parameter::create($data);
		}
	}


	/**
	 * Detach an existing OID from the NetElementType - since we use pivot table parameter with own MVC we can use standard destroy route
	 */
	// public function detach($id)
	// {
	// 	$devtype = NetElementType::findOrFail($id);
	// 	$devtype->oids()->detach(array_keys(\Request::input('ids')));

	// 	return \Redirect::back();
	// }


	/**
	 * Detach all attached OIDs from a NetElementType
	 */
	public function detach_all($id)
	{
		// $devtype = NetElementType::findOrFail($id);
		// $oids 	 = array_keys($devtype->oids->keyBy('id')->all());

		// $devtype->oids()->detach($oids);

		Parameter::where('netelementtype_id', '=', $id)->delete();

		return \Redirect::back();
	}


	/**
	 * This Function gives the Opportunity to quickly set html_frame or html_id of multiple Parameters 
	 * to order the Netelement Controlling View
	 * Note: Input comes from NetElementType.settings.blade.php
	 *
	 * @param 	id  	Integer 	NetElementType ID
	 * @return 	Edit View of NetElementType
	 */
	public function settings($id)
	{
		if (!\Request::has('param_id'))
			return \Redirect::back();

		$html_frame = \Request::input('html_frame');
		$html_id 	= \Request::input('html_id');

		if (!$html_frame && !$html_id)
			return \Redirect::back();

		$params = Parameter::find(\Request::input('param_id'));

		// TODO: If this gets slow we could easily optimize it by doing direct sql updates
		foreach ($params as $param)
		{
			if ($html_frame)
				$param->html_frame = $html_frame;

			if ($html_id)
				$param->html_id = $html_id;

			$param->save();
		}

		return \Redirect::route('NetElementType.edit', $id);
	}

}
