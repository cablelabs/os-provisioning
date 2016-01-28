<?php

namespace Modules\HfcSnmp\Entities;


class Device extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'device';


	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];


	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
			'name' => 'required',
			'ip' => 'ip',
			'community_ro' => 'regex:/(^[A-Za-z0-9]+$)+/',
			'community_rw' => 'regex:/(^[A-Za-z0-9]+$)+/',
			'devicetype_id'=> 'required|exists:devicetype,id|min:1'
        );
    }

	// Placeholder
	public static function get_view_header()
	{
		return 'Device';
	}

	// Placeholder
	public function get_view_link_title()
	{
		return $this->name;
	}    

	/**
	 * link with devicetype
	 */
	public function devicetype()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\DeviceType');
	}

	public function view_belongs_to ()
	{
		return $this->devicetype;
	}

    /**
     * return all DeviceType Objects for Device
     */
    public function devicetypes ()
    {
        return DeviceType::all();
    }


}