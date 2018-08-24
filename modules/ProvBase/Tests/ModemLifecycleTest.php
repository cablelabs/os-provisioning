<?php

namespace Modules\ProvBase\Tests;

use Modules\ProvBase\Entities\Modem;

class ModemLifecycleTest extends \Tests\BaseLifecycleTest
{
    // modem can only be created from Contract.edit
    protected $create_from_model_context = '\Modules\ProvBase\Entities\Contract';

    // fields to be used in update test
    protected $update_fields = [
        'mac',
        'company',
        'department',
        'salutation',
        'firstname',
        'lastname',
        'street',
        'house_number',
        'zip',
        'city',
        'configfile_id',
    ];
}
