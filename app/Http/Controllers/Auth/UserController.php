<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Bouncer;
use Session;
use App\Role;
use App\User;
use Carbon\Carbon;
use App\Exceptions\AuthException;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\BaseViewController;

class UserController extends BaseController
{
    protected $many_to_many = [
        [
            'field' => 'roles_ids',
        ],
    ];

    /**
     * Defines the formular fields for the edit and create view.
     * The labels need to be the same as in the database.
     */
    public function view_form_fields($model = null)
    {
        $current_user = Auth::user();
        $current_user_rank = $current_user->getHighestRank();
        $user_model_rank = $model->exists ? $model->getHighestRank() : 0;

        $languageDirectories = BaseViewController::getAllLanguages();
        $languages = BaseViewController::generateLanguageArray($languageDirectories)
                    ->put('browser', 'Browser');

        $view_header_links = BaseViewController::view_main_menus();
        $dashboard_options = ['Dashboard.index' => 'Dashboard'];
        foreach ($view_header_links as $module_name => $typearray) {
            if (isset($typearray['link'])) {
                $dashboard_options[$typearray['link']] = $typearray['translated_name'];
            }
            if (isset($typearray['submenu'])) {
                foreach ($typearray['submenu'] as $type => $valuearray) {
                    $dashboard_options[$valuearray['link']] = ($typearray['translated_name'] ?? $module_name).': '.$type;
                }
            }
        }

        if ($model->exists &&
             $current_user != $model &&
             $current_user->isNotAn('admin') &&
             $current_user_rank <= $user_model_rank) {
            throw new AuthException(trans('Not allowed to acces this user').'!');
        }

        return [
            ['form_type' => 'text', 'name' => 'login_name', 'description' => 'Login'],
            ['form_type' => 'password', 'name' => 'password', 'description' => 'Password'],
            ['form_type' => 'password', 'name' => 'password_confirmation', 'description' => 'Confirm Password'],
            ['form_type' => 'text', 'name' => 'first_name', 'description' => 'Firstname'],
            ['form_type' => 'text', 'name' => 'last_name', 'description' => 'Lastname'],
            ['form_type' => 'text', 'name' => 'email', 'description' => 'Email'],
            ['form_type' => 'select', 'name' => 'language', 'description' => 'Language',
                'value' => $languages,
                'help' => trans('helper.translate').' https://crowdin.com/project/nmsprime', ],
            ['form_type' => 'checkbox', 'name' => 'active', 'description' => 'Active',
                'value' => '1', 'checked' => true, ],
            ['form_type' => 'select', 'name' => 'roles_ids[]', 'description' => 'Assign Role',
                'value' => $model->html_list(Role::where('rank', '<=', $current_user_rank)->get(), 'name'),
                'options' => [
                    'multiple' => 'multiple',
                    Bouncer::can('update', User::class) ? '' : 'disabled' => 'true', ],
                'help' => trans('helper.assign_role'),
                'selected' => $model->html_list($model->roles, 'name'), ],
            ['form_type' => 'select', 'name' => 'initial_dashboard', 'description' => 'Default login page',
                'value' => $dashboard_options,
            ],
        ];
    }

    public function prepare_input($data)
    {
        $data = parent::prepare_input($data);

        // Dont require password and password confirmation on updating a User (e.g. language)
        if (! $data['password']) {
            $route = \Route::getCurrentRoute();

            if (isset($route->parameters()['User']) && $route->parameters()['User']) {
                unset($data['password'], $data['password_confirmation']);
            }
        }

        return $data;
    }

    public function prepare_input_post_validation($data)
    {
        if (isset($data['password'])) {
            $data['password'] = \Hash::make($data['password']);
            $data['password_changed_at'] = Carbon::now();
            session()->forget('GlobalNotification.shouldChangePassword');
        }

        Bouncer::refresh();

        return parent::prepare_input_post_validation($data);
    }

    public function edit($id)
    {
        $view = parent::edit($id);

        if (\Route::currentRouteName() == 'User.profile') {
            $form_update = 'Profile.update';
            $headline = '';

            return $view->with(compact('form_update', 'headline'));
        }

        return $view;
    }

    public function store($redirect = true)
    {
        parent::store($redirect);

        // if validation fails redirect back
        if (! empty(Session::get('errors'))) {
            return redirect()->back();
        }

        Session::push('tmp_success_above_index_list', trans('messages.created'));

        return redirect()->route('User.index');
    }

    public function update($id)
    {
        parent::update($id);

        return redirect()->back();
    }
}
