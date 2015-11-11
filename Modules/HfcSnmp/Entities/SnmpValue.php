<?php

namespace Modules\HfcSnmp\Entities;

class SnmpValue extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'snmpvalue';

	// Don't forget to fill this array
	protected $fillable = ['device_id', 'snmpmib_id', 'value', 'oid_index'];


	// Add your validation rules here
    public static function rules($id = null)
    {
        return array(
        );
    }
    
    // Name of View
    public static function get_view_header()
    {
        return 'Temporary Testing SNMP Values';
    }

    // link title in index view
    public function get_view_link_title()
    {
        return $this->id.': '.$this->device_id.' - '.$this->snmpmib_id.' - '.$this->oid_index;
    }	

}