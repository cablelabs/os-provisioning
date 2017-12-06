<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Contract;
use \Modules\ProvBase\Http\Controllers\ContractController;

/**
 * Run the lifecycle test for Contract.
 */
class ContractLifecycleTest extends \BaseLifecycleTest {

	// creating a contract twice using the same data is OK;
	// the only unique field is the contract number which is calculated on the fly
	protected $creating_twice_should_fail = False;

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
