<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Qos;
use \Modules\ProvBase\Http\Controllers\QosController;

/**
 * Run the lifecycle test for Contract.
 */
class QosLifecycleTest extends \BaseLifecycleTest {

	protected $creating_twice_should_fail = False;

	// fields to be used in update test
	protected $update_fields = [
		'ds_rate_max',
		'us_rate_max',
	];
}
