<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

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
		$this->app['validator']->extend('ip_in_range', 'Acme\Validators\ExtendedValidator@validateIpInRange');
		$this->app['validator']->extend('ip_larger', 'Acme\Validators\ExtendedValidator@ipLarger');
		$this->app['validator']->extend('netmask', 'Acme\Validators\ExtendedValidator@netmask');
		$this->app['validator']->extend('not_null', 'Acme\Validators\ExtendedValidator@notNull');
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
