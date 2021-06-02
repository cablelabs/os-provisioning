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
    protected $table = 'global_config';

    // Add your validation rules here
    public function rules()
    {
        return [
            'mail' => 'nullable|email',
            'default_country_code' => 'regex:/^[A-Z]{2}$/',
            'passwordResetInterval' => 'min:0,integer',
        ];
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
        $version = exec("rpm -q nmsprime-base --queryformat '%{version}'");
        if (preg_match('/not installed/', $version)) {
            $branch = exec('cd '.app_path().' && git rev-parse --abbrev-ref HEAD');
            $commit = exec('cd '.app_path().' && git rev-parse --short HEAD');
            $github = 'https://github.com/nmsprime/nmsprime/commits/'.exec('cd '.app_path().' && git rev-parse HEAD');

            $version = '<b>GIT</b>: '.$branch.' - '.'<a target=_blank class="text-success" href='.$github.'>'.$commit.'</a>';
        }

        return $version;
    }
}
