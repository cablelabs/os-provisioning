<?php

namespace Modules\ProvVoip\Tests;

class PhonenumberLifecycleTest extends \BaseLifecycleTest
{
    // modem can only be created from Modem.edit
    protected $create_from_model_context = '\Modules\ProvVoip\Entities\Mta';

    // fields to be used in update test
    protected $update_fields = [
        'username',
        'password',
    ];
}
