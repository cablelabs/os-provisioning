<?php

namespace Modules\ProvBase\Tests;

use Modules\ProvBase\Entities\Domain;

class DomainLifecycleTest extends \BaseLifecycleTest
{
    // ATM validators allow creating a domain twice using the same data
    protected $creating_twice_should_fail = false;

    // fields to be used in update test
    protected $update_fields = [
        'alias',
    ];
}
