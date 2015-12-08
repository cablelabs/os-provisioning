<?php namespace Modules\hfcbase\Providers;

use Illuminate\Support\ServiceProvider;

class HfcBaseServiceProvider extends ServiceProvider {

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
		'Modules\HfcBase\Console\TreeBuildCommand',
        ];


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\View::addNamespace('hfcbase', __DIR__.'/../Resources/views');

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
