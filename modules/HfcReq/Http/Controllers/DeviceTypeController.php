<?php

namespace Modules\HfcReq\Http\Controllers;

use Modules\HfcReq\Entities\DeviceType;
use Modules\HfcSnmp\Entities\OID;

class DeviceTypeController extends \BaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'vendor', 'description' => 'Vendor'),
			array('form_type' => 'text', 'name' => 'version', 'description' => 'Version'),
			array('form_type' => 'text', 'name' => 'parent_id', 'description' => 'Parent Device Type'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}


	/**
	 * Assign OIDs to DeviceType - Store in pivot/intermediate-table
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

		$devtype = DeviceType::findOrFail($id);
		$devtype->oids()->attach($oids);

		return \Redirect::route('DeviceType.edit', $devtype->id);
	}


	/**
	 * Return the View for Assigning existing OIDs to the Devicetype
	 */
	public function assign($id)
	{
		$view_var 		= Devicetype::findOrFail($id);
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

		$oids_raw = OID::get(['id', 'name', 'oid']);
		foreach ($oids_raw as $key => $oid)
			$oids[$oid->id] = $oid->name.' - '.$oid->oid; 

		return \View::make('hfcreq::devicetype.assign', $this->compact_prep_view(compact('view_header', 'headline', 'view_var', 'oids', 'mibs')));
	}


	/**
	 * Attach single chosen OIDs (multiselect) to DeviceType - Store in pivot/intermediate-table
	 *
	 * @param 	$id 			integer 	device type
	 * @input 					array 		IDs of the OIDs we want to attach to the given device type
	 */
	public function attach($id)
	{
		$devtype = DeviceType::findOrFail($id);
		$devtype->oids()->attach(\Request::input('oid_id'));

		return \Redirect::route('DeviceType.edit', $devtype->id);
	}


	/**
	 * Detach an existing OID from the Devicetype
	 */
	public function detach($id)
	{
		$devtype = DeviceType::findOrFail($id);
		$devtype->oids()->detach(array_keys(\Request::input('ids')));

		return \Redirect::back();
	}

	/**
	 * Detach all attached OID from the Devicetype
	 */
	public function detach_all($id)
	{
		$devtype = DeviceType::findOrFail($id);
		$oids 	 = array_keys($devtype->oids->keyBy('id')->all());

		$devtype->oids()->detach($oids);

		return \Redirect::back();
	}

}
