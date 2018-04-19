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
        return $this->hasMany('Modules\BillingBase\Entities\Price');
    }


    // Name of View
    public static function view_headline()
    {
        return 'QoS';
    }

    // View Icon
    public static function view_icon()
    {
      return '<i class="fa fa-ticket"></i>';
    }

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label()
	{
		$bsclass = $this->get_bsclass();

		return ['table' => $this->table,
				'index_header' => [$this->table.'.name', $this->table.'.ds_rate_max', $this->table.'.us_rate_max'],
				'header' =>  $this->name,
                'bsclass' => $bsclass,
                'edit' => ['ds_rate_max' => 'unit_ds_rate_max', 'us_rate_max' => 'unit_ds_rate_max'],
				'order_by' => ['0' => 'asc']];
	}

	public function get_bsclass()
	{
        $bsclass = 'success';
        return $bsclass;
    }

    public function unit_ds_rate_max()
    {
        return $this->ds_rate_max.' MBit/s';
    }

    public function unit_us_rate_max()
    {
        return $this->us_rate_max.' MBit/s';
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
    }

    public function updating($q)
    {
    }
}
