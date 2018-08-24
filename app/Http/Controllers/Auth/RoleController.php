<?php

namespace App\Http\Controllers\Auth;

use Bouncer;
use App\Role;
use App\User;
use App\Http\Controllers\BaseController;

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
            ['form_type' => 'select', 'name' => 'users_ids[]', 'description' => 'Assign Users',
                'value' => $model->html_list(User::all(), 'login_name'),
                'options' => [
                    'multiple' => 'multiple',
                    (Bouncer::can('update', User::class) && Bouncer::can('update', Role::class)) ? '' : 'disabled' => 'true', ],
                    'help' => trans('helper.assign_users'),
                    'selected' => $model->html_list($model->users, 'name'), ],
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
        $modelAbilities = AbilityController::getModelAbilities($data['view_var']);

        return $view->with(compact('roleAbilities', 'roleForbiddenAbilities', 'modelAbilities', 'customAbilities', 'actions'));
    }
}
