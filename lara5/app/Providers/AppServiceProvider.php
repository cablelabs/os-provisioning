<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// use old laravel 4.2 blade tag syntax, NOTE: delete all cached files under storage/views/ when we change this
		\Blade::setRawTags('{{', '}}');
		// \Blade::setContentTags('{{{', '}}}');
		// \Blade::setEscapedContentTags('{{{', '}}}');
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
