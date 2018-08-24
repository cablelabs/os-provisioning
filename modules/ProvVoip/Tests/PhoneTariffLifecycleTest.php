<?php

namespace Modules\ProvVoip\Tests;

use \Modules\ProvVoip\Entities\PhoneTariff;
use \Modules\ProvVoip\Http\Controllers\PhoneTariffController;

class PhoneTariffLifecycleTest extends \BaseLifecycleTest {

	// fields to be used in update test
	protected $update_fields = [
        'external_identifier',
        'name',
        'usable',
        'description',
	];

}
