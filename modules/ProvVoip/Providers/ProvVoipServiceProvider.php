<?php

namespace Modules\ProvVoip\Providers;

use Illuminate\Support\ServiceProvider;

class ProvVoipServiceProvider extends ServiceProvider {

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
		'\Modules\ProvVoip\Console\CarrierCodeDatabaseUpdaterCommand',
		'\Modules\ProvVoip\Console\EkpCodeDatabaseUpdaterCommand',
		'\Modules\ProvVoip\Console\TRCClassDatabaseUpdaterCommand',
		'\Modules\ProvVoip\Console\PhonenumberCommand',
		];

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\View::addNamespace('provvoip', __DIR__.'/../Resources/views');
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
