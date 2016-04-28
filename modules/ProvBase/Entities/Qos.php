<?php

namespace Modules\ProvBase\Entities;

use Log;

class Qos extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'qos';

	// Add your validation rules here
	public static function rules($id = null)
    {
        return array(
            'name' => 'required'
        );
    }


    /**
     * Relations
     */
	public function modem()
	{
		return $this->hasMany("Modules\ProvBase\Entities\Modem");
	}


    public function prices()
    {
        return $this->hasMany('Modules\Billingbase\Entities\Price');
    }


    // Name of View
    public static function view_headline()
    {
        return 'QoS';
    }

    // link title in index view
    public function get_view_link_title()
    {
        return ['index' => [$this->name, $this->ds_rate_max.' MBit/s', $this->us_rate_max.' MBit/s'],
                'index_header' => ['Name', 'DS Rate', 'US Rate'],
                'header' => $this->name];
    }

    /**
     * BOOT: init quality observer
     */
    public static function boot()
    {
        parent::boot();

        Qos::observe(new QosObserver);
    }
}

/**
 * Qos Observer Class
 * Handles changes on CMs
 *
 */
class QosObserver
{
    public function creating($q)
    {
    	$q->ds_rate_max_help = $q->ds_rate_max * 1024 * 1024;
    	$q->us_rate_max_help = $q->us_rate_max * 1024 * 1024;
    }

    public function updating($q)
    {
    	$q->ds_rate_max_help = $q->ds_rate_max * 1024 * 1024;
    	$q->us_rate_max_help = $q->us_rate_max * 1024 * 1024;
    }
}
