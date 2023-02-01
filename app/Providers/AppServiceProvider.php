<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::withoutComponentTags();

        Validator::includeUnvalidatedArrayKeys();

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
