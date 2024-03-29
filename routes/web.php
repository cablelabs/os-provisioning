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

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Home Route
Route::get('', ['as' => 'Home', 'uses' => 'WelcomeController@index', 'middleware' => ['web']]);

/*
 * Admin Login Routes
 */
Route::group(['prefix' => 'admin', 'middleware' => ['web']], function () {
    Route::get('login', [
        'as' => 'adminLogin',
        'uses' => 'Auth\LoginController@showLoginForm',
    ]);

    Route::post('login', [
        'as' => 'login.post',
        'uses' => 'Auth\LoginController@login',
    ]);

    Route::post('logout', [
        'as' => 'logout.post',
        'uses' => 'Auth\LoginController@logout',
    ]);
});

Broadcast::routes();

// Core Admin API
BaseRoute::group([], function () {
    BaseRoute::resource('GlobalConfig', 'GlobalConfigController');
    BaseRoute::resource('GuiLog', 'GuiLogController');
    BaseRoute::resource('User', 'Auth\UserController');
    BaseRoute::resource('Role', 'Auth\RoleController');
    BaseRoute::resource('Sla', 'SlaController');

    // As we dont want authorization middleware (only authentication) we can not set the routes with our general function and have to set them manually
    BaseRoute::get('SupportRequest', [
        'uses' => 'SupportRequestController@index',
        'as'   => 'SupportRequest.index',
    ]);
    BaseRoute::post('SupportRequest', [
        'uses' => 'SupportRequestController@store',
        'as'   => 'SupportRequest.store',
    ]);
    BaseRoute::get('SupportRequest/create', [
        'uses' => 'SupportRequestController@create',
        'as'   => 'SupportRequest.create',
    ]);
    BaseRoute::get('SupportRequest/{id}', [
        'uses' => 'SupportRequestController@edit',
        'as'   => 'SupportRequest.edit',
    ]);
    BaseRoute::put('SupportRequest/{id}', [
        'uses' => 'SupportRequestController@update',
        'as'   => 'SupportRequest.update',
    ]);
    BaseRoute::get('SupportRequest/{id}/log', [
        'uses' => '\App\Http\Controllers\GuiLogController@filter',
        'as' => 'SupportRequest.guilog',
    ]);

    BaseRoute::get('globalSearch', [
        'as' => 'Base.globalSearch',
        'uses' => 'BaseController@globalSearch',
    ]);

    BaseRoute::get('Config', [
        'as' => 'Config.index',
        'uses' => 'GlobalConfigController@index',
    ]);

    BaseRoute::get('profile/{id}', [
        'as' => 'User.profile',
        'uses' => 'Auth\UserController@edit',
        'middleware' => ['owns:view,App\User'],
    ]);

    BaseRoute::put('profile/{id}', [
        'as' => 'Profile.update',
        'uses' => 'Auth\UserController@update',
        'middleware' => ['owns:update,App\User'],
    ]);

    BaseRoute::post('user/detach/{id}/{func}', [
        'as' => 'user.detach',
        'uses' => 'Auth\UserController@detach',
        'middleware' => ['can:delete,App\User'],
    ]);

    BaseRoute::post('Role/capability', [
        'as' => 'capability.update',
        'uses' => 'Auth\AbilityController@updateCapability',
        'middleware' => ['can:update,App\Role'],
    ]);

    BaseRoute::post('Role/customAbility', [
        'as' => 'customAbility.update',
        'uses' => 'Auth\AbilityController@updateCustomAbility',
        'middleware' => ['can:update,App\Role'],
    ]);

    BaseRoute::post('Role/modelAbility', [
        'as' => 'modelAbility.update',
        'uses' => 'Auth\AbilityController@updateModelAbility',
        'middleware' => ['can:update,App\Role'],
    ]);

    BaseRoute::get('Guilog/restore/{id}', [
        'as' => 'Guilog.restore',
        'uses' => 'GuiLogController@restoreModel',
        'middleware' => ['can:delete,App\Role'],
    ]);

    BaseRoute::post('Sla/clicked', [
        'as' => 'Sla.clicked_sla',
        'uses' => 'SlaController@clicked_sla',
    ]);

    BaseRoute::post('user/updateGeopos', [
        'as' => 'user.updateGeopos',
        'uses' => 'Auth\UserController@updateGeopos',
        'middleware' => ['can:update,App\User'],
    ]);

    BaseRoute::get('Apps/active', [
        'as' => 'Apps.active',
        'uses' => 'AppController@showApps',
        'middleware' => ['can:view,App\App'],
    ]);

    BaseRoute::get('Apps/inactive', [
        'as' => 'Apps.inactive',
        'uses' => 'AppController@showApps',
        'middleware' => ['can:view,App\App'],
    ]);

    BaseRoute::get('missingModule/{module}', [
        'as' => 'missingModule',
        'uses' => 'BaseController@missingModule',
    ]);

    BaseRoute::get('Notifications', [
        'as' => 'Notifications.index',
        'uses' => 'NotificationController@index',
    ]);

    BaseRoute::get('Notifications/navbar', [
        'as' => 'Notifications.navbar',
        'uses' => 'NotificationController@navbar',
    ]);

    BaseRoute::post('Notifications/markAllRead', [
        'as' => 'Notifications.markAllRead',
        'uses' => 'NotificationController@markAllRead',
    ]);

    BaseRoute::post('Notifications/{notification}', [
        'as' => 'Notifications.markRead',
        'uses' => 'NotificationController@markRead',
    ]);

    BaseRoute::post('Sidebar/setPinnedState', [
        'as' => 'Sidebar.setPinnedState',
        'uses' => 'SessionAndCacheStateController@setPinnedState',
    ]);

    BaseRoute::post('Session/SetActiveTab', [
        'as' => 'App.SetActiveTab',
        'uses' => 'SessionAndCacheStateController@setActiveTab',
    ]);
});
