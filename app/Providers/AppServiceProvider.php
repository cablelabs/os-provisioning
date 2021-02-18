<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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

        Response::macro('v0ApiReply', function ($data = [], $success = false, $id = null, $statusCode = 200) {
            foreach (\App\BaseModel::ABOVE_MESSAGES_ALLOWED_TYPES as $type) {
                foreach (\App\BaseModel::ABOVE_MESSAGES_ALLOWED_PLACES as $place) {
                    if (Session::has("tmp_{$type}_above_{$place}")) {
                        $data['messages']["{$type}s"] = array_merge($data['messages']["{$type}s"] ?? [], Session::get("tmp_{$type}_above_{$place}"));
                    }
                }
            }

            $data['success'] = boolval($success);

            if ($id !== null) {
                $data['id'] = intval($id);
            }

            return Response::json($data, $statusCode);
        });
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
