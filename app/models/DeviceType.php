<?php

namespace Models;


class DeviceType extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'devicetype';


	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['name', 'vendor', 'version', 'description', 'parent_id'];

	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
			'name' => 'required'
        );
    }

	// Name of View
	public static function get_view_header()
	{
		return 'Device Type';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->name;
	}

	/**
	 * link with devices
	 */
	public function devices()
	{
		return $this->hasMany('Models\Device', 'devicetype_id');
	}

	/**
	 * link with 
	 */
	public function snmpmibs()
	{
		return $this->hasMany('Models\SnmpMib', 'devicetype_id');
	}
}