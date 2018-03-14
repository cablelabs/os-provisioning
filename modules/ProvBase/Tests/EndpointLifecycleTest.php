<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Endpoint;
use \Modules\ProvBase\Http\Controllers\EndpointController;

class EndpointLifecycleTest extends \BaseLifecycleTest {

	// fields to be used in update test
	protected $update_fields = [
		'mac',
		'description',
	];
}
