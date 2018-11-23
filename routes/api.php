<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app(Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('register', 'App\Api\V1\Controllers\AuthController@register');
        $api->post('login', 'App\Api\V1\Controllers\AuthController@login');
        $api->get('user', 'App\Api\V1\Controllers\AuthController@user');
        $api->get('token', 'App\Api\V1\Controllers\AuthController@token');
    });

    $api->group(['middleware' => ['api.auth']], function ($api) {
        $api->group(['prefix' => 'expenses'], function ($api) {
            $api->get('/', 'App\Api\V1\Controllers\ExpensesController@index');
            $api->post('/', 'App\Api\V1\Controllers\ExpensesController@store');
            $api->group(['middleware' => ['bindings', 'can:edit-expense,expense']], function($api){
                $api->put('/{expense}', 'App\Api\V1\Controllers\ExpensesController@update');
                $api->delete('/{expense}', 'App\Api\V1\Controllers\ExpensesController@destroy');
            });

            $api->get('/all', 'App\Api\V1\Controllers\ExpensesController@all')->middleware('can:manage-expenses');
        });

        $api->group(['prefix' => 'users', 'middleware' => ['bindings', 'can:manage-users']], function ($api) {
            $api->get('/', 'App\Api\V1\Controllers\UsersController@index');
            $api->post('/', 'App\Api\V1\Controllers\UsersController@store');
            $api->put('/{user}', 'App\Api\V1\Controllers\UsersController@update');
            $api->delete('/{user}', 'App\Api\V1\Controllers\UsersController@destroy');
        });
    });
});
