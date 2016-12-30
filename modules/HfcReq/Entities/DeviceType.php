<?php

namespace Modules\HfcReq\Entities;

class DeviceType extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'devicetype';


	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

	/**
	 * View Stuff
	 */

	// Name of View
	public static function view_headline()
	{
		// Note: single underscore has error in view as consequence
		return 'Device  Type';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->name, $this->vendor, $this->version],
		        'index_header' => ['Name', 'Vendor', 'Version'],
		        'header' => $this->name];
	}

	// returns all objects that are related to a DeviceType
	public function view_has_many()
	{
		// return array(
		// 	'NetElement' => $this->netelements,
		// 	'OID' 		=> $this->oids,
		// );
		$ret['Base']['NetElement']['class'] 	= 'NetElement';
		$ret['Base']['NetElement']['relation']  = $this->netelements;

		if (\PPModule::is_active('hfcsnmp'))
		{
			// extra page or on Base ??
			// $ret['Base']['- Assign OIDs from MIB']['view']['view'] = 'hfcreq::devicetype.add_oid_from_mib';
			// $mibs = \Modules\HfcSnmp\Entities\MibFile::select(['id', 'name', 'version'])->get();
			// $ret['Base']['- Assign OIDs from MIB']['view']['vars']['list'] = isset($mibs[0]) ? $mibs[0]->html_list($mibs, 'name', true) : [];

			// $ret['Base']['OID']['class'] 	= 'OID';
			// $ret['Base']['OID']['relation'] = $this->oids;
			// $ret['Base']['OID']['options']['hide_delete_button'] = 0;
			// $ret['Base']['OID']['options']['hide_create_button'] = 0;
			$ret['Base']['OIDs']['view']['view'] = 'hfcreq::devicetype.oids';
			$ret['Base']['OIDs']['view']['vars']['list'] = $this->oids;

		}

		return $ret;
	}


	/**
	 * Relations
	 */
	public function netelements()
	{
		return $this->hasMany('Modules\HfcReq\Entities\NetElement', 'devicetype_id');
	}

	public function oids()
	{
		return $this->belongsToMany('Modules\HfcSnmp\Entities\OID', 'devicetype_oid', 'devicetype_id', 'oid_id')->orderBy('oid');
	}

	public function netelementtype()
	{
		return $this->belongsTo('Modules\HfcReq\Entities\NetElementType', 'devicetype_id');
	}
}