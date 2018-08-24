<?php

namespace Modules\ProvBase\Tests;

use Modules\ProvBase\Http\Controllers\IpPoolController;

class IpPoolLifecycleTest extends \BaseLifecycleTest
{
    // modem can only be created from Cmts.edit
    protected $create_from_model_context = '\Modules\ProvBase\Entities\Cmts';

    // create form is filled with initial data from IpPoolController
    protected $creating_empty_should_fail = false;

    // do not create using fake data – TODO: this needs rewriting of the seeder to match
    // the models validation rules
    protected $tests_to_be_excluded = ['testCreateWithFakeData', 'testCreateTwiceUsingTheSameData'];

    // fields to be used in update test
    protected $update_fields = [
        'dns2_ip',
        'dns3_ip',
        'description',
    ];
}
