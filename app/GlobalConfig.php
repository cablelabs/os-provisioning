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

namespace App;

class GlobalConfig extends BaseModel
{
    // The associated SQL table for this Model
    public $table = 'global_config';

    public $guarded = ['login_img_upload'];

    // Add your validation rules here
    public function rules()
    {
        return [
            'mail' => 'nullable|email',
            'default_country_code' => 'regex:/^[A-Z]{2}$/',
            'password_reset_interval' => 'min:0,integer',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::updated(function () {
            cache()->forget('GlobalConfig');
        });
    }

    // Name of View
    public static function view_headline()
    {
        return 'Global Config';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'Global Config';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-book"></i>';
    }

    /*
     * Get NMS Version
     * NOTE: get the actual rpm version of the installed package
     *       or branch name and short commit reference of GIT repo
     *
     * @param: null
     * @return: string containing version information
     * @author: Torsten Schmidt
     */
    public function version()
    {
        if (! cache()->has('installType')) {
            $version = exec("rpm -q nmsprime-base --queryformat '%{version}'");
            cache(['installType' => preg_match('/not installed/', $version) ? 'git' : $version]);
        }

        if (($type = cache('installType')) !== 'git') {
            return  $type;
        }

        if (cache('gitStats', optional([]))['commitShort'] === exec('cd '.app_path().' && git rev-parse --short HEAD')) {
            return cache('gitStats');
        }

        $gitStats = [
            'branch' => exec('cd '.app_path().' && git rev-parse --abbrev-ref HEAD'),
            'commitShort' => exec('cd '.app_path().' && git rev-parse --short HEAD'),
            'commitLong' => exec('cd '.app_path().' && git rev-parse HEAD'),
            'repo' => exec('cd '.app_path()." && git config --get remote.origin.url | sed -r 's/.*(\\@|\\/\\/)(.*)(\\:|\\/)([^:\\/]*)\\/([^\\/\\.]*)\\.git/https:\\/\\/\\2\\/\\4\\/\\5/' | sed 's/.*\\/\\([^ ]*\\/[^.]*\\).*/\\1/'"),
        ];
        cache(compact('gitStats'));

        return $gitStats;
    }
}
