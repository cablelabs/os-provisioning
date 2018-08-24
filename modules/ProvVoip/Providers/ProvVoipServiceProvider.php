<?php

namespace Modules\ProvVoip\Providers;

use Illuminate\Support\ServiceProvider;

class ProvVoipServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('provvoip.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'provvoip'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/provvoip');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/provvoip';
        }, \Config::get('view.paths')), [$sourcePath]), 'provvoip');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/provvoip');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'provvoip');
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'provvoip');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
