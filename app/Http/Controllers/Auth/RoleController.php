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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Role;
use App\User;
use Bouncer;

class RoleController extends BaseController
{
    protected $edit_left_md_size = 4;
    protected $edit_right_md_size = 8;
    protected $many_to_many = [
        [
            'field' => 'users_ids',
            'classes' => [User::class, Role::class],
        ],
    ];

    public function view_form_fields($model = null)
    {
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            ['form_type' => 'text', 'name' => 'title', 'description' => 'Title'],
            ['form_type' => 'text', 'name' => 'description', 'description' => 'Description'],
            ['form_type' => 'text', 'name' => 'rank', 'description' => 'Rank', 'help' => trans('helper.assign_rank')],
            [
                'form_type' => 'select',
                'name' => 'users_ids[]',
                'description' => 'Assign Users',
                'value' => $model->html_list(User::all(), 'login_name'),
                'options' => [
                    'multiple' => 'multiple',
                    (Bouncer::can('update', User::class) && Bouncer::can('update', Role::class)) ? '' : 'disabled',
                ],
                'help' => trans('helper.assign_users'),
                'selected' => $model->html_list($model->users, 'name'),
            ],
        ];
    }

    public function edit($id)
    {
        Bouncer::refresh();

        $view = parent::edit($id);

        $data = $view->getData();
        $actions = AbilityController::getCrudActions();
        $roleAbilities = AbilityController::mapCustomAbilities($data['view_var']->getAbilities());
        $roleForbiddenAbilities = AbilityController::mapCustomAbilities($data['view_var']->getForbiddenAbilities());

        $customAbilities = AbilityController::getCustomAbilities();
        $capabilities = AbilityController::getCapabilities($data['view_var']);
        $modelAbilities = AbilityController::getModelAbilities($data['view_var']);

        return $view->with(compact('roleAbilities', 'roleForbiddenAbilities', 'modelAbilities', 'customAbilities', 'capabilities', 'actions'));
    }
}
