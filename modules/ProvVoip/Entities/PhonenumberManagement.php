<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class PhonenumberManagement extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'phonenumbermanagement';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'phonenumber_id' => 'required|exists:phonenumber,id|min:1',
		);
	}

	// Don't forget to fill this array
	protected $fillable = [
					'phonenumber_id',
					'activation_date',
					'porting_in',
					'carrier_in',
					'deactivation_date',
					'porting_out',
					'carrier_out',
					'subscriber_company',
					'subscriber_salutation',
					'subscriber_academic_degree',
					'subscriber_firstname',
					'subscriber_lastname',
					'subscriber_street',
					'subscriber_house_number',
					'subscriber_zip',
					'subscriber_city',
					'subscriber_country_id'
				];

	public function __construct() {
		parent::__construct();
	}


	// Name of View
	public static function get_view_header()
	{
		return 'Phonenumbers Management';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "(".$this->country_code.") ".$this->prefix_number."/".$this->number;
	}

	/**
	 * ALL RELATIONS
	 * link with mtas
	 */
	public function phonenumber()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\Phonenumber');
	}

}
