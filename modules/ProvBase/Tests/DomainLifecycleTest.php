<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Domain;
use \Modules\ProvBase\Http\Controllers\DomainController;

class DomainLifecycleTest extends \BaseLifecycleTest {

	// ATM validators allow creating a domain twice using the same data
	protected $creating_twice_should_fail = False;

	// fields to be used in update test
	protected $update_fields = [
		'alias',
	];
}
