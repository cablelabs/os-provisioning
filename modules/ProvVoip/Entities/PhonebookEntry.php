<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class PhonebookEntry extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'phonebookentry';


	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
		);
	}


	// Name of View
	public static function get_view_header()
	{
		return 'Phonebook entry';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->id;
	}

	/**
	 * ALL RELATIONS
	 * link with phonenumbers
	 */
	public function phonenumbermanagement()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\PhonenumberManagement');
	}

	// belongs to an phonenumber
	public function view_belongs_to ()
	{
		return $this->phonenumbermanagement;
	}
}
