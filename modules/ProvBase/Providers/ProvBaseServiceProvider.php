<?php

namespace Modules\ProvBase\Providers;

use Illuminate\Support\ServiceProvider;

class ProvBaseServiceProvider extends ServiceProvider
{
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
        'Modules\ProvBase\Console\ConfigfileCommand',
        'Modules\ProvBase\Console\ContractCommand',
        'Modules\ProvBase\Console\CpeHostnameCommand',
        'Modules\ProvBase\Console\DhcpCommand',
        'Modules\ProvBase\Console\ImportCommand',
        'Modules\ProvBase\Console\ImportTvCustomersCommand',
        'Modules\ProvBase\Console\ImportNetUserCommand',
        'Modules\ProvBase\Console\GeocodeCommand',
        'Modules\ProvBase\Console\HardwareSupportCommand',
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
            __DIR__.'/../Config/config.php' => config_path('provbase.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../Config/netgw.php', 'provbase.netgw'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'provbase'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/provbase');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/provbase';
        }, \Config::get('view.paths')), [$sourcePath]), 'provbase');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/provbase');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'provbase');
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'provbase');
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
