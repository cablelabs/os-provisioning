<?php

namespace Modules\ProvVoip\Tests;

use \Modules\ProvVoip\Entities\Mta;
use \Modules\ProvVoip\Http\Controllers\MtaController;

class MtaLifecycleTest extends \BaseLifecycleTest {

	// modem can only be created from Modem.edit
	protected $create_from_model_context = '\Modules\ProvBase\Entities\Modem';

}
