<?php

namespace Modules\ProvVoip\Tests;

class PhoneTariffLifecycleTest extends \BaseLifecycleTest
{
    // fields to be used in update test
    protected $update_fields = [
        'external_identifier',
        'name',
        'usable',
        'description',
    ];
}
