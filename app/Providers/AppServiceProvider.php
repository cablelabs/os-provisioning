<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('DivOpen', function ($expression) {
            return "<?php echo Form::openDivClass($expression); ?>";
        });

        Blade::directive('DivClose', function () {
            return '<?php echo Form::closeDivClass(); ?>';
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Facade to Object binding
        $this->app->bind('chanellog', 'Acme\log\ChannelWriter');
    }
}
