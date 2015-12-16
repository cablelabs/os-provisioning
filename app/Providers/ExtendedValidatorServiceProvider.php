<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExtendedValidatorServiceProvider extends ServiceProvider {

	/**
	* Bootstrap any necessary services.
	*
	* @return void
	*/
	public function boot()
	{

		/*
		 * Extend Validator Class
		 * see extensions/validators/..
		 */
		$this->app['validator']->extend('ip', 'Acme\Validators\ExtendedValidator@validateIpaddr');
		$this->app['validator']->extend('mac', 'Acme\Validators\ExtendedValidator@validateMac');
		$this->app['validator']->extend('geopos', 'Acme\Validators\ExtendedValidator@validateGeopos');
		$this->app['validator']->extend('docsis', 'Acme\Validators\ExtendedValidator@validateDocsis');
		$this->app['validator']->extend('dateornull', 'Acme\Validators\ExtendedValidator@validateDateOrNull');
	}

	/**
	* Register the service provider.
	*
	* @return void
	*/
	public function register()
	{

	}

}
