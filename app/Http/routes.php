<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
 */
/*
Route::group(['middleware' => ['web']], function () {
    //
});
 */



/*
 * Include  Routes
 */

$root_dir = '/var/www/pms_api/app/Api/';
$api = app('Dingo\Api\Routing\Router');

include $root_dir.'V1/routes.php';

/* $api->version('v1', function ($api) { */
/*     $api->group(['namespace' => 'App\Api\V1\Controllers'], function($api) { */
/*         /1* $api->post('/auth/login', 'Auth\AuthController@authenticate'); *1/ */
/*         /1* $api->post('/auth/register', 'Auth\AuthController@register'); *1/ */
/*         include '/var/www/pms_api/app/Api/V1/Auth/routes.php'; */
/*         $api->group(['middleware' => 'jwt.auth'], function($api) { */
/*             $api->get('/users/all', 'User\UserController@show'); */
/*             $api->get('/users/one', 'User\UserController@getAuthenticatedUser'); */
/*             $api->post('/company/add', 'Company\CompanyController@add'); */
/*         }); */
/*     }); */
/* }); */




