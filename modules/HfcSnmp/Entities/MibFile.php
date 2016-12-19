<?php

namespace Modules\HfcSnmp\Entities;

class MibFile extends \BaseModel {

	public $table = 'mibfile';

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'MIB-File';
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->name, $this->version],
				'index_header' => ['Name', 'Version'],
				'header' => $this->name];
	}

	/**
	 * Relations
	 */
	public function oids()
	{
		return $this->hasMany('Modules\HfcSnmp\Entities\OID');
	}


	public function view_has_many ()
	{
		return array(
			'OID' => $this->oids,
		);
	}
}