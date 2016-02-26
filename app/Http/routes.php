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

$root_dir = '/var/www/dingo/app/Api/';
$api = app('Dingo\Api\Routing\Router');

// V1
require_once $root_dir.'V1/routes.php';

