<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Contract;
use \Modules\ProvBase\Http\Controllers\ContractController;

/**
 * Run the lifecycle test for Contract.
 */
class ContractLifecycleTest extends \BaseLifecycleTest {

	// fields to be used in update test
	protected $update_fields = [
		'company',
		'department',
		'salutation',
		'academic_degree',
		'firstname',
		'lastname',
		'street',
		'house_number',
		'zip',
		'city',
	];
}
