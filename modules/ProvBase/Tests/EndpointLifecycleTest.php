<?php

namespace Modules\ProvBase\Tests;

class EndpointLifecycleTest extends \BaseLifecycleTest
{
    // modem can only be created from Modem.edit
    protected $create_from_model_context = '\Modules\ProvBase\Entities\Modem';

    // fields to be used in update test
    protected $update_fields = [
        'mac',
        'description',
    ];
}
