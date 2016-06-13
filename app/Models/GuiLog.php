<?php

namespace App;

class GuiLog extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'guilog';


	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			// 'mail' => 'email',
		);
	}

	// Name of View
	public static function view_headline()
	{
		return 'Logs';
	}

	// link title in index view
	public function view_index_label()
	{
		return "1";
	}


}