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
		'Modules\ProvBase\Console\aclCommand',
		'Modules\ProvBase\Console\configfileCommand',
		'Modules\ProvBase\Console\contractCommand',
		'Modules\ProvBase\Console\dhcpCommand',
		'Modules\ProvBase\Console\importCommand',
		'Modules\ProvBase\Console\importTvCustomersCommand',
		'Modules\ProvBase\Console\importNetUserCommand',
		'Modules\ProvBase\Console\geocodeCommand',
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

		$this->mergeConfigFrom(__DIR__ . '/../Config/dates.php', 'dates');
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
