<?php

namespace Modules\ProvVoip\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class PhoneTariff extends \BaseModel {

    // The associated SQL table for this Model
	public $table = 'phonetariff';


	public static function get_purchase_tariffs() {

		return array('foo' => 'bar');
	}

}
