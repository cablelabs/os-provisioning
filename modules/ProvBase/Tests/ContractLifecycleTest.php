<?php

namespace Modules\ProvBase\Tests;

use Modules\ProvBase\Entities\Contract;

/**
 * Run the lifecycle test for Contract.
 */
class ContractLifecycleTest extends \Tests\BaseLifecycleTest
{
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
