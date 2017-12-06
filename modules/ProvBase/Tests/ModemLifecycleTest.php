<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Modem;
use \Modules\ProvBase\Http\Controllers\ModemController;

class ModemLifecycleTest extends \BaseLifecycleTest {

	// modem can only be created from Contract.edit
	protected $create_from_model_context = '\Modules\ProvBase\Entities\Contract';

}
