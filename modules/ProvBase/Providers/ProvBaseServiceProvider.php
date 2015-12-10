<?php namespace Modules\provbase\Providers;

use Illuminate\Support\ServiceProvider;

class ProvBaseServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;


	/**
	 * The artisan commands provided by this module
	 */
	protected $commands = [
		'Modules\ProvBase\Console\dhcpCommand',
		'Modules\ProvBase\Console\configfileCommand',
	];


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\View::addNamespace('provbase', __DIR__.'/../Resources/views');

		$this->commands($this->commands);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
