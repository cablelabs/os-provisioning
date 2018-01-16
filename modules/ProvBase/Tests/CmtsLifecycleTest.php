<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Cmts;
use \Modules\ProvBase\Http\Controllers\CmtsController;

class CmtsLifecycleTest extends \BaseLifecycleTest {

	// fields to be used in update test
	protected $update_fields = [
		'ip',
		'company',
	];
}
