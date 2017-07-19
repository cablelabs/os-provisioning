<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Base class to derive lifecycle tests for a model from
 *
 * This will reuse the static get_fake_data method in your model's seeder class (e.g. for create).
 * Assure that your seeder is up do date and running!
 *
 * @author Patrick Reichel
 */
class BaseLifecycleTest extends TestCase {

	public function __construct() {
		parent::__construct();

		$this->_get_seeder();
	}


	protected function _get_seeder() {

		$class_name = get_called_class();
		$model_name = str_replace('LifecycleTest', '', $class_name);
		$seeder_class_name = $model_name."Seeder";

		$this->seeder = $seeder_class_name;
	}

	protected function _get_fake_data() {

		return call_user_func($this->seeder."::get_fake_data()");
	}
}
