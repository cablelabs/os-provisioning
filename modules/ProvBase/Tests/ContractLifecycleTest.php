<?php

namespace Modules\ProvBase\Tests;

use \Modules\ProvBase\Entities\Contract;
use \Modules\ProvBase\Http\Controllers\ContractController;

class ContractLifecycleTest extends \BaseLifecycleTest {

	protected $seeder = '\Modules\ProvBase\Database\Seeders\ContractTableSeeder';

	protected $controller = '\Modules\ProvBase\Http\Controllers\ContractController';

	protected $model_name = 'Contract';

	protected $database_table = 'contract';

}
