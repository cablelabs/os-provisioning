<?php

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

// Core Admin API
BaseRoute::group([], function () {
    BaseRoute::resource('GlobalConfig', 'GlobalConfigController');
    BaseRoute::resource('GuiLog', 'GuiLogController');
    BaseRoute::resource('User', 'Auth\UserController');
    BaseRoute::resource('Role', 'Auth\RoleController');
    BaseRoute::resource('Sla', 'SlaController');

    // As we dont want authorization middleware (only authentication) we can not set the routes with our general function and have to set them manually
    BaseRoute::get("SupportRequest", [
        'uses' => "SupportRequestController@index",
        'as'   => "SupportRequest.index",
        ]);
    BaseRoute::post("SupportRequest", [
        'uses' => "SupportRequestController@store",
        'as'   => "SupportRequest.store",
        ]);
    BaseRoute::get("SupportRequest/create", [
        'uses' => "SupportRequestController@create",
        'as'   => "SupportRequest.create",
        ]);
    BaseRoute::get("SupportRequest/{id}", [
        'uses' => "SupportRequestController@edit",
        'as'   => "SupportRequest.edit",
        ]);
    BaseRoute::put("SupportRequest/{id}", [
        'uses' => "SupportRequestController@update",
        'as'   => "SupportRequest.update",
        ]);
    BaseRoute::get("SupportRequest/{id}/log", [
        'uses' => '\App\Http\Controllers\GuiLogController@filter',
        'as' => "SupportRequest.guilog",
    ]);


    BaseRoute::get('base/fulltextSearch', [
        'as' => 'Base.fulltextSearch',
        'uses' => 'BaseController@fulltextSearch',
    ]);

    BaseRoute::get('Config', [
        'as' => 'Config.index',
        'uses' => 'GlobalConfigController@index',
    ]);

    BaseRoute::get('profile/{id}', [
        'as' => 'User.profile',
        'uses' => 'Auth\UserController@edit',
        'middleware' => ["owns:view,App\User"],
    ]);

    BaseRoute::post('user/detach/{id}/{func}', [
        'as' => 'user.detach',
        'uses' => 'Auth\UserController@detach',
        'middleware' => ["can:delete,App\User"],
    ]);

    BaseRoute::post('Role/customAbility', [
        'as' => 'customAbility.update',
        'uses' => 'Auth\AbilityController@updateCustomAbility',
        'middleware' => ["can:update,App\Role"],
    ]);

    BaseRoute::post('Role/modelAbility', [
        'as' => 'modelAbility.update',
        'uses' => 'Auth\AbilityController@updateModelAbility',
        'middleware' => ["can:update,App\Role"],
    ]);

    BaseRoute::get('Guilog/restore/{id}', [
        'as' => 'Guilog.restore',
        'uses' => 'GuiLogController@restoreModel',
        'middleware' => ["can:delete,App\Role"],
    ]);

    BaseRoute::post('Sla/clicked', [
        'as' => 'Sla.clicked_sla',
        'uses' => 'SlaController@clicked_sla',
    ]);

});
